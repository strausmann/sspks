<?php

namespace SSpkS\Package;

use SSpkS\Device\DeviceList;

/**
 * SPK PackageFinder class.
 */
class PackageFilter
{
    private $config;
    private $pkgList;
    /** @var bool|string[] Array of allowed architectures, or FALSE to ignore. */
    private $filterArch = false;
    /** @var bool|string Target firmware version, or FALSE to ignore. */
    private $filterFwVersion = false;
    /** @var bool|string Channel 'stable' or 'beta', or FALSE to ignore. */
    private $filterChannel = false;
    /** @var bool TRUE to return unique packages with latest version only. */
    private $filterOldVersions = false;

    /**
     * @param \SSpkS\Config            $config  Config object
     * @param \SSpkS\Package\Package[] $pkgList List of Package objects to filter
     */
    public function __construct(\SSpkS\Config $config, array $pkgList)
    {
        $this->config = $config;
        $this->pkgList = $pkgList;
    }

    /**
     * Sets the architecture to filter for.
     *
     * @param string $arch architecture
     */
    public function setArchitectureFilter($arch)
    {
        // Specific corner case
        if ($arch == '88f6282') {
            $arch = '88f6281';
        }

        $dl = new DeviceList($this->config);
        $family = $dl->getFamily($arch);
        $this->filterArch = array_unique(['noarch', $arch, $family]);
    }

    /**
     * Sets the firmware version to filter for.
     *
     * @param string|bool $version Firmware version in dotted notation ('1.2.3456') or FALSE to ignore.
     */
    public function setFirmwareVersionFilter($version)
    {
        $this->filterFwVersion = $version;
    }

    /**
     * Sets the channel to filter for.
     *
     * @param string $channel Channel ('stable' or 'beta')
     */
    public function setChannelFilter($channel)
    {
        $this->filterChannel = $channel;
    }

    /**
     * Enables or disables omitting older versions of the same package from the result set.
     *
     * @param bool $status TRUE to enable the filter, FALSE to disable
     */
    public function setOldVersionFilter($status)
    {
        $this->filterOldVersions = $status;
    }

    /**
     * If filter is enabled, checks if architecture of $package is compatible to requested one.
     *
     * @param \SSpkS\Package\Package $package package to test
     *
     * @return bool TRUE if matching, or FALSE
     */
    public function isMatchingArchitecture($package)
    {
        if ($this->filterArch === false) {
            return true;
        }
        $matches = array_intersect($this->filterArch, $package->arch);

        return count($matches) > 0;
    }

    /**
     * If filter is enabled, checks if minimal firmware required of $package is
     * smaller or equal to system firmware.
     *
     * @param \SSpkS\Package\Package $package package to test
     *
     * @return bool TRUE if matching, or FALSE
     */
    public function isMatchingFirmwareVersion($package)
    {
        if ($this->filterFwVersion === false) {
            return true;
        }

        if (isset($package->firmware)) {
            //echo '<pre>';
            //echo 'Step1 Package:'.$package->package.' | PackVer : '.$package->version.' | MyDSMVer: '.$this->filterFwVersion.'  | Min: '.$package->firmware;
            //echo '</pre>';
            if (version_compare($package->firmware, '7.0.0', '>')) {
                return true;
            }

            return false;
        }

        if (isset($package->os_min_ver) and !isset($package->os_max_ver)) {
            //echo '<pre>';
            //echo 'Step2 Package:'.$package->package.' | PackVer: '.$package->version.' | MyDSMVer: '.$this->filterFwVersion.' | Min: '.$package->os_min_ver.' | Max: '.$package->os_max_ver;
            //echo '</pre>';
            if (version_compare($package->os_min_ver, $this->filterFwVersion, '<=')) {
                return true;
            }

            return false;
        }

        if (isset($package->os_min_ver) and isset($package->os_max_ver)) {
            //echo '<pre>';
            //echo 'Step3 Package:'.$package->package.' | PackVer: '.$package->version.' | MyDSMVer: '.$this->filterFwVersion.' | Min: '.$package->os_min_ver.' | Max: '.$package->os_max_ver;
            //echo '</pre>';
            if (version_compare($package->os_min_ver, $this->filterFwVersion, '<=') and version_compare($package->os_max_ver, $this->filterFwVersion, '>')) {
                return true;
            }

            return false;
        }
    }

    /**
     * If filter is enabled, checks if channel of $package matches requested one.
     * 'beta' will show ALL packages, also those from 'stable'.
     *
     * @param \SSpkS\Package\Package $package package to test
     *
     * @return bool TRUE if matching, or FALSE
     */
    public function isMatchingChannel($package)
    {
        if ($this->filterChannel === false) {
            return true;
        }
        if ($this->filterChannel == 'stable' && $package->isBeta() === false) {
            return true;
        } elseif ($this->filterChannel == 'beta') {
            return true;
        }

        return false;
    }

    /**
     * Removes older versions of same package from $pkgList.
     *
     * @param \SSpkS\Package\Package[] $pkgList List of packages
     *
     * @return \SSpkS\Package\Package[] List of unique packages
     */
    public function removeObsoleteVersions($pkgList)
    {
        $uniqueList = [];
        foreach ($pkgList as $package) {
            $pkgId = $package->package;
            if (isset($uniqueList[$pkgId]) && version_compare($uniqueList[$pkgId]->version, $package->version, '>=')) {
                continue;
            }
            $uniqueList[$pkgId] = $package;
        }

        return array_values($uniqueList);
    }

    /**
     * Returns the list of packages matching the currently set filters.
     *
     * @return \SSpkS\Package\Package[] list of Package objects matching filters
     */
    public function getFilteredPackageList()
    {
        $filteredPackages = [];
        foreach ($this->pkgList as $package) {
            if (!$this->isMatchingArchitecture($package)) {
                continue;
            }
            if (!$this->isMatchingFirmwareVersion($package)) {
                continue;
            }
            if (!$this->isMatchingChannel($package)) {
                continue;
            }
            $filteredPackages[] = $package;
        }
        if ($this->filterOldVersions) {
            // remove older versions of duplicate packages from $filteredPackages
            $filteredPackages = $this->removeObsoleteVersions($filteredPackages);
        }

        return $filteredPackages;
    }
}
