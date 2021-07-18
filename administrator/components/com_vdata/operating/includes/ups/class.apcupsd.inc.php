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
class Apcupsd extends UPS
{
    
    private $_output = array();

    
    public function __construct()
    {
        parent::__construct();
        if (defined('PSI_UPS_APCUPSD_LIST') && is_string(PSI_UPS_APCUPSD_LIST)) {
            if (preg_match(ARRAY_EXP, PSI_UPS_APCUPSD_LIST)) {
                $upses = eval(PSI_UPS_APCUPSD_LIST);
            } else {
                $upses = array(PSI_UPS_APCUPSD_LIST);
            }
            foreach ($upses as $ups) {
                CommonFunctions::executeProgram('apcaccess', 'status '.trim($ups), $temp);
                if (! empty($temp)) {
                    $this->_output[] = $temp;
                }
            }
        } else { //use default if address and port not defined
            CommonFunctions::executeProgram('apcaccess', 'status', $temp);
            if (! empty($temp)) {
                $this->_output[] = $temp;
            }
        }
    }

    
    private function _info()
    {
        foreach ($this->_output as $ups) {

            $dev = new UPSDevice();

            // General info
            if (preg_match('/^UPSNAME\s*:\s*(.*)$/m', $ups, $data)) {
                $dev->setName(trim($data[1]));
            }
            if (preg_match('/^MODEL\s*:\s*(.*)$/m', $ups, $data)) {
                $model=trim($data[1]);
                if (preg_match('/^APCMODEL\s*:\s*(.*)$/m', $ups, $data)) {
                    $dev->setModel($model.' ('.trim($data[1]).')');
                } else {
                    $dev->setModel($model);
                }
            }
            if (preg_match('/^UPSMODE\s*:\s*(.*)$/m', $ups, $data)) {
                $dev->setMode(trim($data[1]));
            }
            if (preg_match('/^STARTTIME\s*:\s*(.*)$/m', $ups, $data)) {
                $dev->setStartTime(trim($data[1]));
            }
            if (preg_match('/^STATUS\s*:\s*(.*)$/m', $ups, $data)) {
                $dev->setStatus(trim($data[1]));
            }
            if (preg_match('/^ITEMP\s*:\s*(.*)$/m', $ups, $data)) {
                $dev->setTemperatur(trim($data[1]));
            }
            // Outages
            if (preg_match('/^NUMXFERS\s*:\s*(.*)$/m', $ups, $data)) {
                $dev->setOutages(trim($data[1]));
            }
            if (preg_match('/^LASTXFER\s*:\s*(.*)$/m', $ups, $data)) {
                $dev->setLastOutage(trim($data[1]));
            }
            if (preg_match('/^XOFFBATT\s*:\s*(.*)$/m', $ups, $data)) {
                $dev->setLastOutageFinish(trim($data[1]));
            }
            // Line
            if (preg_match('/^LINEV\s*:\s*(\d*\.\d*)(.*)$/m', $ups, $data)) {
                $dev->setLineVoltage(trim($data[1]));
            }
            if (preg_match('/^LINEFREQ\s*:\s*(\d*\.\d*)(.*)$/m', $ups, $data)) {
                $dev->setLineFrequency(trim($data[1]));
            }
            if (preg_match('/^LOADPCT\s*:\s*(\d*\.\d*)(.*)$/m', $ups, $data)) {
                $dev->setLoad(trim($data[1]));
            }
            // Battery
            if (preg_match('/^BATTDATE\s*:\s*(.*)$/m', $ups, $data)) {
                $dev->setBatteryDate(trim($data[1]));
            }
            if (preg_match('/^BATTV\s*:\s*(\d*\.\d*)(.*)$/m', $ups, $data)) {
                $dev->setBatteryVoltage(trim($data[1]));
            }
            if (preg_match('/^BCHARGE\s*:\s*(\d*\.\d*)(.*)$/m', $ups, $data)) {
                $dev->setBatterCharge(trim($data[1]));
            }
            if (preg_match('/^TIMELEFT\s*:\s*(\d*\.\d*)(.*)$/m', $ups, $data)) {
                $dev->setTimeLeft(trim($data[1]));
            }
            $this->upsinfo->setUpsDevices($dev);
        }
    }

    
    public function build()
    {
        $this->_info();
    }
}
