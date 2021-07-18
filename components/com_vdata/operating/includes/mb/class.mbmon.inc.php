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
class MBMon extends Sensors
{
   
    private $_lines = array();

   
    public function __construct()
    {
        parent::__construct();
        switch (strtolower(PSI_SENSOR_ACCESS)) {
        case 'tcp':
            $fp = fsockopen("localhost", 411, $errno, $errstr, 5);
            if ($fp) {
                $lines = "";
                while (!feof($fp)) {
                    $lines .= fread($fp, 1024);
                }
                $this->_lines = preg_split("/\n/", $lines, -1, PREG_SPLIT_NO_EMPTY);
            } else {
                $this->error->addError("fsockopen()", $errno." ".$errstr);
            }
            break;
        case 'command':
            CommonFunctions::executeProgram('mbmon', '-c 1 -r', $lines, PSI_DEBUG);
            $this->_lines = preg_split("/\n/", $lines, -1, PREG_SPLIT_NO_EMPTY);
            break;
        case 'file':
            if (CommonFunctions::rfts(APP_ROOT.'/data/mbmon.txt', $lines)) {
                $this->_lines = preg_split("/\n/", $lines, -1, PREG_SPLIT_NO_EMPTY);
            }
            break;
        default:
            $this->error->addConfigError('__construct()', 'PSI_SENSOR_ACCESS');
            break;
        }
    }

    /**
     * get temperature information
     *
     * @return void
     */
    private function _temperature()
    {
        foreach ($this->_lines as $line) {
            if (preg_match('/^(TEMP\d*)\s*:\s*(.*)$/D', $line, $data)) {
                if ($data[2] <> '0') {
                    $dev = new SensorDevice();
                    $dev->setName($data[1]);
                    $dev->setMax(70);
                    if ($data[2] < 250) {
                        $dev->setValue($data[2]);
                    }
                    $this->mbinfo->setMbTemp($dev);
                }
            }
        }
    }

    /**
     * get fan information
     *
     * @return void
     */
    private function _fans()
    {
        foreach ($this->_lines as $line) {
            if (preg_match('/^(FAN\d*)\s*:\s*(.*)$/D', $line, $data)) {
                if ($data[2] <> '0') {
                    $dev = new SensorDevice();
                    $dev->setName($data[1]);
                    $dev->setValue($data[2]);
                    $dev->setMax(3000);
                    $this->mbinfo->setMbFan($dev);
                }
            }
        }
    }

    /**
     * get voltage information
     *
     * @return void
     */
    private function _voltage()
    {
        foreach ($this->_lines as $line) {
            if (preg_match('/^(V.*)\s*:\s*(.*)$/D', $line, $data)) {
                if ($data[2] <> '+0.00') {
                    $dev = new SensorDevice();
                    $dev->setName($data[1]);
                    $dev->setValue($data[2]);
                    $this->mbinfo->setMbVolt($dev);
                }
            }
        }
    }

    /**
     * get the information
     *
     * @see PSI_Interface_Sensor::build()
     *
     * @return void
     */
    public function build()
    {
        $this->_temperature();
        $this->_voltage();
        $this->_fans();
    }
}
