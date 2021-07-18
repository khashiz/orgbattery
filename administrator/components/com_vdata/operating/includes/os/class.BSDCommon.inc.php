<?php
/*------------------------------------------------------------------------
# com_vdata - vData
# ------------------------------------------------------------------------
# author    Team WDMtech
# copyright Copyright (C) 2016 www.wdmtech.com. All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Websites: http://www.wdmtech.com
# Technical Support:  Forum - http://www.wdmtech.com/support-forum
-----------------------------------------------------------------------*/
// No direct access
defined('_JEXEC') or die('Restricted access');
abstract class BSDCommon extends OS
{
    
    private $_dmesg = array();

    
    private $_CPURegExp1 = "";

    
    private $_CPURegExp2 = "";

    
    private $_SCSIRegExp1 = "";

    
    private $_SCSIRegExp2 = "";

    
    private $_PCIRegExp1 = "";

    
    private $_PCIRegExp2 = "";

    
    public function __construct()
    {
        parent::__construct();
    }

   
    protected function setCPURegExp1($value)
    {
        $this->_CPURegExp1 = $value;
    }

    
    protected function setCPURegExp2($value)
    {
        $this->_CPURegExp2 = $value;
    }

    
    protected function setSCSIRegExp1($value)
    {
        $this->_SCSIRegExp1 = $value;
    }

    
    protected function setSCSIRegExp2($value)
    {
        $this->_SCSIRegExp2 = $value;
    }

    /**
     * setter for pciregexp1
     *
     * @param string $value value to set
     *
     * @return void
     */
    protected function setPCIRegExp1($value)
    {
        $this->_PCIRegExp1 = $value;
    }

    /**
     * setter for pciregexp2
     *
     * @param string $value value to set
     *
     * @return void
     */
    protected function setPCIRegExp2($value)
    {
        $this->_PCIRegExp2 = $value;
    }

    /**
     * read /var/run/dmesg.boot, but only if we haven't already
     *
     * @return array
     */
    protected function readdmesg()
    {
        if (count($this->_dmesg) === 0) {
            if (PSI_OS != "Darwin") {
                if (CommonFunctions::rfts('/var/run/dmesg.boot', $buf, 0, 4096, false) || CommonFunctions::rfts('/var/log/dmesg.boot', $buf, 0, 4096, false) || CommonFunctions::rfts('/var/run/dmesg.boot', $buf)) {  // Once again but with debug
                    $parts = preg_split("/rebooting|Uptime/", $buf, -1, PREG_SPLIT_NO_EMPTY);
                    $this->_dmesg = preg_split("/\n/", $parts[count($parts) - 1], -1, PREG_SPLIT_NO_EMPTY);
                }
            }
        }

        return $this->_dmesg;
    }

    /**
     * get a value from sysctl command
     *
     * @param string $key key for the value to get
     *
     * @return string
     */
    protected function grabkey($key)
    {
        $buf = "";
        if (CommonFunctions::executeProgram('sysctl', "-n $key", $buf, PSI_DEBUG)) {
            return $buf;
        } else {
            return '';
        }
    }

    /**
     * Virtual Host Name
     *
     * @return void
     */
    protected function hostname()
    {
        if (PSI_USE_VHOST === true) {
            $this->sys->setHostname(getenv('SERVER_NAME'));
        } else {
            if (CommonFunctions::executeProgram('hostname', '', $buf, PSI_DEBUG)) {
                $this->sys->setHostname($buf);
            }
        }
    }

    /**
     * IP of the Canonical Host Name
     *
     * @return void
     */
    protected function ip()
    {
        if (PSI_USE_VHOST === true) {
            $this->sys->setIp(gethostbyname($this->sys->getHostname()));
        } else {
            if (!($result = getenv('SERVER_ADDR'))) {
                $this->sys->setIp(gethostbyname($this->sys->getHostname()));
            } else {
                $this->sys->setIp($result);
            }
        }
    }

    /**
     * Kernel Version
     *
     * @return void
     */
    protected function kernel()
    {
        $s = $this->grabkey('kern.version');
        $a = preg_split('/:/', $s);
        $this->sys->setKernel($a[0].$a[1].':'.$a[2]);
    }

    /**
     * Number of Users
     *
     * @return void
     */
    protected function users()
    {
        if (CommonFunctions::executeProgram('who', '| wc -l', $buf, PSI_DEBUG)) {
            $this->sys->setUsers($buf);
        }
    }

    /**
     * Processor Load
     * optionally create a loadbar
     *
     * @return void
     */
    protected function loadavg()
    {
        $s = $this->grabkey('vm.loadavg');
        $s = preg_replace('/{ /', '', $s);
        $s = preg_replace('/ }/', '', $s);
        $this->sys->setLoad($s);
        if (PSI_LOAD_BAR && (PSI_OS != "Darwin")) {
            if ($fd = $this->grabkey('kern.cp_time')) {
                // Find out the CPU load
                // user + sys = load
                // total = total
                preg_match($this->_CPURegExp2, $fd, $res);
                $load = $res[2] + $res[3] + $res[4]; // cpu.user + cpu.sys
                $total = $res[2] + $res[3] + $res[4] + $res[5]; // cpu.total
                // we need a second value, wait 1 second befor getting (< 1 second no good value will occour)
                sleep(1);
                $fd = $this->grabkey('kern.cp_time');
                preg_match($this->_CPURegExp2, $fd, $res);
                $load2 = $res[2] + $res[3] + $res[4];
                $total2 = $res[2] + $res[3] + $res[4] + $res[5];
                $this->sys->setLoadPercent((100 * ($load2 - $load)) / ($total2 - $total));
            }
        }
		if(PSI_OS == "Darwin"){
			$cpu_load = 0;
			if(!empty($s)){
			$cpu_load = explode('',$s);	
			$cpu_load =  $cpu_load[0];
			}
			
			$this->sys->setLoadPercent($cpu_load);
			
		}
    }

    /**
     * CPU information
     *
     * @return void
     */
    protected function cpuinfo()
    {
        $dev = new CpuDevice();
        $dev->setModel($this->grabkey('hw.model'));
        $notwas = true;
        foreach ($this->readdmesg() as $line) {
            if ($notwas) {
               if (preg_match("/".$this->_CPURegExp1."/", $line, $ar_buf)) {
                    $dev->setCpuSpeed(round($ar_buf[2]));
                    $notwas = false;
                }
            } else {
                if (preg_match("/ Origin| Features/", $line, $ar_buf)) {
                    if (preg_match("/ Features2[ ]*=.*<(.*)>/", $line, $ar_buf)) {
                        $feats = preg_split("/,/", strtolower(trim($ar_buf[1])), -1, PREG_SPLIT_NO_EMPTY);
                        foreach ($feats as $feat) {
                            if (($feat=="vmx") || ($feat=="svm")) {
                                $dev->setVirt($feat);
                                break 2;
                            }
                        }
                        break;
                    }
                } else break;
            }
        }
        $ncpu = $this->grabkey('hw.ncpu');
        if (is_null($ncpu) || (trim($ncpu) == "") || (!($ncpu >= 1)))
            $ncpu = 1;
        for ($ncpu ; $ncpu > 0 ; $ncpu--) {
            $this->sys->setCpus($dev);
        }
    }

    /**
     * SCSI devices
     * get the scsi device information out of dmesg
     *
     * @return void
     */
    protected function scsi()
    {
        foreach ($this->readdmesg() as $line) {
            if (preg_match("/".$this->_SCSIRegExp1."/", $line, $ar_buf)) {
                $dev = new HWDevice();
                $dev->setName($ar_buf[1].": ".$ar_buf[2]);
                $this->sys->setScsiDevices($dev);
            } elseif (preg_match("/".$this->_SCSIRegExp2."/", $line, $ar_buf)) {
                /* duplication security */
                $notwas = true;
                foreach ($this->sys->getScsiDevices() as $finddev) {
                    if ($notwas && (substr($finddev->getName(), 0, strpos($finddev->getName(), ': ')) == $ar_buf[1])) {
                        $finddev->setCapacity($ar_buf[2] * 2048 * 1.049);
                        $notwas = false;
                        break;
                    }
                }
                if ($notwas) {
                    $dev = new HWDevice();
                    $dev->setName($ar_buf[1]);
                    $dev->setCapacity($ar_buf[2] * 2048 * 1.049);
                    $this->sys->setScsiDevices($dev);
                }
            }
        }
        /* cleaning */
        foreach ($this->sys->getScsiDevices() as $finddev) {
                    if (strpos($finddev->getName(), ': ') !== false)
                        $finddev->setName(substr(strstr($finddev->getName(), ': '), 2));
        }
    }

    /**
     * parsing the output of pciconf command
     *
     * @return Array
     */
    protected function pciconf()
    {
        $arrResults = array();
        $intS = 0;
        if (CommonFunctions::executeProgram("pciconf", "-lv", $strBuf, PSI_DEBUG)) {
            $arrTemp = array();
            $arrBlocks = preg_split("/\n\S/", $strBuf, -1, PREG_SPLIT_NO_EMPTY);
            foreach ($arrBlocks as $strBlock) {
                $arrLines = preg_split("/\n/", $strBlock, -1, PREG_SPLIT_NO_EMPTY);
                $vend = null;
                foreach ($arrLines as $strLine) {
                    if (preg_match("/\sclass=0x([a-fA-F0-9]{4})[a-fA-F0-9]{2}\s.*\schip=0x([a-fA-F0-9]{4})([a-fA-F0-9]{4})\s/", $strLine, $arrParts)) {
                        $arrTemp[$intS] = 'Class '.$arrParts[1].': Device '.$arrParts[3].':'.$arrParts[2];
                        $vend = '';
                    } elseif (preg_match("/(.*) = '(.*)'/", $strLine, $arrParts)) {
                        if (trim($arrParts[1]) == "vendor") {
                            $vend = trim($arrParts[2]);
                        } elseif (trim($arrParts[1]) == "device") {
                            if (($vend !== null) && ($vend !== '')) {
                                $arrTemp[$intS] = $vend." - ".trim($arrParts[2]);
                            } else {
                                $arrTemp[$intS] = trim($arrParts[2]);
                                $vend = '';
                            }
                        }
                    }
                }
                if ($vend !== null) {
                    $intS++;
                }
            }
            foreach ($arrTemp as $name) {
                $dev = new HWDevice();
                $dev->setName($name);
                $arrResults[] = $dev;
            }
        }

        return $arrResults;
    }

    /**
     * PCI devices
     * get the pci device information out of dmesg
     *
     * @return void
     */
    protected function pci()
    {
        if (!is_array($results = Parser::lspci(false)) || !is_array($results = $this->pciconf())) {
            foreach ($this->readdmesg() as $line) {
                if (preg_match("/".$this->_PCIRegExp1."/", $line, $ar_buf)) {
                    $dev = new HWDevice();
                    $dev->setName($ar_buf[1].": ".$ar_buf[2]);
                    $results[] = $dev;
                } elseif (preg_match("/".$this->_PCIRegExp2."/", $line, $ar_buf)) {
                    $dev = new HWDevice();
                    $dev->setName($ar_buf[1].": ".$ar_buf[2]);
                    $results[] = $dev;
                }
            }
        }
        foreach ($results as $dev) {
            $this->sys->setPciDevices($dev);
        }
    }

    /**
     * IDE devices
     * get the ide device information out of dmesg
     *
     * @return void
     */
    protected function ide()
    {
        foreach ($this->readdmesg() as $line) {
            if (preg_match('/^(ad[0-9]+): (.*)MB <(.*)> (.*) (.*)/', $line, $ar_buf)) {
                $dev = new HWDevice();
                $dev->setName($ar_buf[1].": ".$ar_buf[3]);
                $dev->setCapacity($ar_buf[2] * 1024);
                $this->sys->setIdeDevices($dev);
            } elseif (preg_match('/^(acd[0-9]+): (.*) <(.*)> (.*)/', $line, $ar_buf)) {
                $dev = new HWDevice();
                $dev->setName($ar_buf[1].": ".$ar_buf[3]);
                $this->sys->setIdeDevices($dev);
            } elseif (preg_match('/^(ada[0-9]+): <(.*)> (.*)/', $line, $ar_buf)) {
                $dev = new HWDevice();
                $dev->setName($ar_buf[1].": ".$ar_buf[2]);
                $this->sys->setIdeDevices($dev);
            } elseif (preg_match('/^(ada[0-9]+): (.*)MB \((.*)\)/', $line, $ar_buf)) {
                /* duplication security */
                $notwas = true;
                foreach ($this->sys->getIdeDevices() as $finddev) {
                    if ($notwas && (substr($finddev->getName(), 0, strpos($finddev->getName(), ': ')) == $ar_buf[1])) {
                        $finddev->setCapacity($ar_buf[2] * 1024);
                        $notwas = false;
                        break;
                    }
                }
                if ($notwas) {
                    $dev = new HWDevice();
                    $dev->setName($ar_buf[1]);
                    $dev->setCapacity($ar_buf[2] * 1024);
                    $this->sys->setIdeDevices($dev);
                }
            }
        }
        /* cleaning */
        foreach ($this->sys->getIdeDevices() as $finddev) {
                    if (strpos($finddev->getName(), ': ') !== false)
                        $finddev->setName(substr(strstr($finddev->getName(), ': '), 2));
        }
    }

    /**
     * Physical memory information and Swap Space information
     *
     * @return void
     */
    protected function memory()
    {
        if (PSI_OS == 'FreeBSD' || PSI_OS == 'OpenBSD') {
            // vmstat on fbsd 4.4 or greater outputs kbytes not hw.pagesize
            // I should probably add some version checking here, but for now
            // we only support fbsd 4.4
            $pagesize = 1024;
        } else {
            $pagesize = $this->grabkey('hw.pagesize');
        }
        if (CommonFunctions::executeProgram('vmstat', '', $vmstat, PSI_DEBUG)) {
            $lines = preg_split("/\n/", $vmstat, -1, PREG_SPLIT_NO_EMPTY);
            $ar_buf = preg_split("/\s+/", trim($lines[2]), 19);
            if (PSI_OS == 'NetBSD' || PSI_OS == 'DragonFly') {
                $this->sys->setMemFree($ar_buf[4] * 1024);
            } else {
                $this->sys->setMemFree($ar_buf[4] * $pagesize);
            }
            $this->sys->setMemTotal($this->grabkey('hw.physmem'));
            $this->sys->setMemUsed($this->sys->getMemTotal() - $this->sys->getMemFree());

            if (((PSI_OS == 'OpenBSD' || PSI_OS == 'NetBSD') && CommonFunctions::executeProgram('swapctl', '-l -k', $swapstat, PSI_DEBUG)) || CommonFunctions::executeProgram('swapinfo', '-k', $swapstat, PSI_DEBUG)) {
                $lines = preg_split("/\n/", $swapstat, -1, PREG_SPLIT_NO_EMPTY);
                foreach ($lines as $line) {
                    $ar_buf = preg_split("/\s+/", $line, 6);
                    if (($ar_buf[0] != 'Total') && ($ar_buf[0] != 'Device')) {
                        $dev = new DiskDevice();
                        $dev->setMountPoint($ar_buf[0]);
                        $dev->setName("SWAP");
                        $dev->setFsType('swap');
                        $dev->setTotal($ar_buf[1] * 1024);
                        $dev->setUsed($ar_buf[2] * 1024);
                        $dev->setFree($dev->getTotal() - $dev->getUsed());
                        $this->sys->setSwapDevices($dev);
                    }
                }
            }
        }
    }

    /**
     * USB devices
     * get the ide device information out of dmesg
     *
     * @return void
     */
    protected function usb()
    {
        foreach ($this->readdmesg() as $line) {
//            if (preg_match('/^(ugen[0-9\.]+): <(.*)> (.*) (.*)/', $line, $ar_buf)) {
//                    $dev->setName($ar_buf[1].": ".$ar_buf[2]);
            if (preg_match('/^(u[a-z]+[0-9]+): <([^,]*)(.*)> on (usbus[0-9]+)/', $line, $ar_buf)) {
                    $dev = new HWDevice();
                    $dev->setName($ar_buf[2]);
                    $this->sys->setUSBDevices($dev);
            }
        }
    }

    /**
     * filesystem information
     *
     * @return void
     */
    protected function filesystems()
    {
        $arrResult = Parser::df();
        foreach ($arrResult as $dev) {
            $this->sys->setDiskDevices($dev);
        }
    }

    /**
     * Distribution
     *
     * @return void
     */
    protected function distro()
    {
        if (CommonFunctions::executeProgram('uname', '-s', $result, PSI_DEBUG)) {
            $this->sys->setDistribution($result);
        }
    }

    /**
     * get the information
     *
     * @see PSI_Interface_OS::build()
     *
     * @return Void
     */
    public function build()
    {
        $this->distro();
        $this->memory();
        $this->ide();
        $this->pci();
        $this->cpuinfo();
        $this->filesystems();
        $this->kernel();
        $this->users();
        $this->loadavg();
        $this->hostname();
        $this->ip();
        $this->scsi();
        $this->usb();
    }
}
