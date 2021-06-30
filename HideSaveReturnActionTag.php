<?php
/*
 * © 2021 Mass General Brigham (formerly Partners HealthCare). All Rights Reserved.
 * @author David Clark
 * Heavily influenced  by work of Luke Stevcens
 * 
 * This is an external module that hides the submit button on surveys
 * 
 * The trigger is similar to REDCap "action tags".
 * @HIDESAVEANDRETURN would hide the submit button for an instrument in which this 
 * tag exists
 */
 namespace MGB\HideSaveReturnActionTag;

use ExternalModules\AbstractExternalModule;
use ExternalModules\ExternalModules;

class HideSaveReturnActionTag extends AbstractExternalModule
{
    protected $is_survey = 0;

    protected  static $Tags =  array('@HIDESAVEANDRETURN' => array('description' => 'Hide the default ß on survey pages  that have this field visible. ') );

    public function redcap_data_entry_form_top($project_id, $record = null, $instrument, $event_id, $group_id = null, $repeat_instance = 1)
    {
        $this->includeTagFunctions($instrument);
    }

    public function redcap_survey_page_top($project_id, $record = null, $instrument, $event_id, $group_id = null, $survey_hash = null, $response_id = null, $repeat_instance = 1)
    {
        $this->is_survey = 1;
        $this->includeTagFunctions($instrument);
    }
    /**
     * Augment the action_tag_explain content on project Design pages by 
     * adding some additional tr following the last built-in action tag.
     * @param type $project_id
     */
    public function redcap_every_page_before_render($project_id)
    {
            if (PAGE === 'Design/action_tag_explain.php') {
            global $lang;
            $lastActionTagDesc = end(\Form::getActionTags()) ;

            // which $lang element is this?
            $langElement = array_search($lastActionTagDesc, $lang);
            $config = $this->getConfig();

            foreach (static::$Tags as $tag => $tagAttr) {
                $lastActionTagDesc .= "</td></tr>";
                $lastActionTagDesc .= $this->makeTagTR($tag, $config['description']);
            }
            $lang[$langElement] = rtrim(rtrim(rtrim(trim($lastActionTagDesc), '</tr>')), '</td>');
        }
    }


    protected function includeTagFunctions($instrument)
    {
        $taggedFields = array();
        $tags = static::$Tags;
        $instrumentFields = \REDCap::getDataDictionary('array', false, true, $instrument);

        $pattern = '/(' . implode('|', array_keys($tags)) . ')/';        
        foreach ($instrumentFields as $fieldName => $fieldDetails) {
            $matches = array();
            if (preg_match($pattern, $fieldDetails['field_annotation'], $matches)) {
                      $taggedFields[$fieldName] = $fieldDetails;
            }
        }
        if (count($taggedFields) > 0) {
            $this->includeJS($taggedFields);
        }
    }
 
    public function includeJS($taggedFields)
    {
        print "<script type=\"text/javascript\">" .
        "$(document).ready(function() {" .
        "if ( typeof($(\"[name = 'submit-btn-savereturnlater']\").val()) != \"undefined\" ) {" .
        "   $(\"[name = 'submit-btn-savereturnlater']\").css('display','none');" .
        "}" .
        "});" .
        "</script>";

    }


    /**
     * Make a table row for an action tag copied from 
     * v8.5.0/Design/action_tag_explain.php
     * @global type $isAjax
     * @param type $tag
     * @param type $description
     * @return type
     */
    protected function makeTagTR($tag, $description)
    {
        global $isAjax, $lang;
        return \RCView::tr(
            array(),
            \RCView::td(
                array('class' => 'nowrap', 'style' => 'text-align:center;background-color:#f5f5f5;color:#912B2B;padding:7px 15px 7px 12px;font-weight:bold;border:1px solid #ccc;border-bottom:0;border-right:0;'),
                ((!$isAjax || (isset($_POST['hideBtns']) && $_POST['hideBtns'] == '1')) ? '' :
                    \RCView::button(array('class' => 'btn btn-xs btn-rcred', 'style' => '', 'onclick' => "$('#field_annotation').val(trim('" . js_escape($tag) . " '+$('#field_annotation').val())); highlightTableRowOb($(this).parentsUntil('tr').parent(),2500);"), $lang['design_171']))
            ) .
                \RCView::td(
                    array('class' => 'nowrap', 'style' => 'background-color:#f5f5f5;color:#912B2B;padding:7px;font-weight:bold;border:1px solid #ccc;border-bottom:0;border-left:0;border-right:0;'),
                    $tag
                ) .
                \RCView::td(
                    array('style' => 'font-size:12px;background-color:#f5f5f5;padding:7px;border:1px solid #ccc;border-bottom:0;border-left:0;'),
                    '<i class="fas fa-cube mr-1"></i>' . $description
                )
        );
    }
 
   
}
