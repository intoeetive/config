<?php

/*
=====================================================
 Config
-----------------------------------------------------
 http://www.intoeetive.com/
-----------------------------------------------------
 Copyright (c) 2018 Yuri Salimovskiy
=====================================================
-----------------------------------------------------
 Purpose: Display config items in ExpressionEngine. MSM-compatible
=====================================================
*/

if (! defined('BASEPATH') && ! defined('EXT')) exit('No direct script access allowed');

$plugin_info = array(
	'pi_name'			=> 'Config',
	'pi_version'		=> '1.0',
	'pi_author'			=> 'Yuri Salimovskiy',
	'pi_author_url'		=> 'http://github.com/intoeetive/config',
	'pi_description'	=> 'Display config items in ExpressionEngine. MSM-compatible',
	'pi_usage'			=> Config::usage()
);


class Config {

	var $return_data = "";

	/** ----------------------------------------
	/**  Constructor
	/** ----------------------------------------*/

	public function __construct($str = '')
	{

		return $this->item();
    }
    
    public function item()
    {
		$item = ee()->TMPL->fetch_param('item');
        
        if (empty($item))
        {
            $this->return_data = ee()->TMPL->no_results();
            return $this->return_data;
        }
        
        if (ee()->TMPL->fetch_param('site_id')=='' && ee()->TMPL->fetch_param('site_name')=='')
        {
            $this->return_data = ee()->config->item($item);
            return $this->return_data;
        }
        else
        {
            ee()->db->select()
                ->from('sites');
            if (ee()->TMPL->fetch_param('site_id')!='')
            {
                ee()->db->where('site_id', ee()->TMPL->fetch_param('site_id'));
            }
            else
            {
                ee()->db->where('site_name', ee()->TMPL->fetch_param('site_name'));
            }
            $site_q = ee()->db->get();
            if ($site_q->num_rows()!=1)
            {
                $this->return_data = ee()->TMPL->no_results();
                return $this->return_data;
            }
            
            $cols = ['site_system_preferences', 'site_member_preferences', 'site_tempate_preferences', 'site_channel_preferences'];
            
            foreach ($cols as $col)
            {
                $prefs = unserialize(base64_decode($site_q->row($col)));
                if (array_key_exists($item, $prefs))
                {
                    $this->return_data = ee()->TMPL->parse_variables_row($prefs[$item], $prefs);
                    return $this->return_data;
                }
            }
        }


	}


	/** ----------------------------------------
	/**  Plugin Usage
	/** ----------------------------------------*/

	public static function usage()
	{
	
		ob_start(); 
		?>
	
{exp:config item="site_url"}
	
{exp:config item="site_url" site_id="2"}    

{exp:config item="site_url" site_name="default_site"}    
		<?php
		$buffer = ob_get_contents();
		
		ob_end_clean(); 
	
		return $buffer;
		
	} // END usage()

} 