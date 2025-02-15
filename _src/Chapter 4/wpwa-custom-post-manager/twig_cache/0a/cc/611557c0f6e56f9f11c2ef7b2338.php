<?php

/* project_meta.html */
class __TwigTemplate_0acc611557c0f6e56f9f11c2ef7b2338 extends Twig_Template
{
    public function __construct(Twig_Environment $env)
    {
        parent::__construct($env);

        $this->parent = false;

        $this->blocks = array(
        );
    }

    protected function doDisplay(array $context, array $blocks = array())
    {
        // line 1
        echo "<input type=\"hidden\" name=\"project_meta_nonce\" value=\"";
        if (isset($context["project_meta_nonce"])) { $_project_meta_nonce_ = $context["project_meta_nonce"]; } else { $_project_meta_nonce_ = null; }
        echo twig_escape_filter($this->env, $_project_meta_nonce_, "html", null, true);
        echo "\" />

<table class=\"form-table\">
    <tr>
        <th style=''><label for='Project Status'>Project Status *</label></th>
        <td>
            <select class='widefat' name=\"sel_project_status\" id=\"sel_project_status\">
                <option ";
        // line 8
        if (isset($context["project_status"])) { $_project_status_ = $context["project_status"]; } else { $_project_status_ = null; }
        if (($_project_status_ == 0)) {
            echo "selected";
        }
        echo " value=\"0\">Select</option>
                <option ";
        // line 9
        if (isset($context["project_status"])) { $_project_status_ = $context["project_status"]; } else { $_project_status_ = null; }
        if (($_project_status_ == "planned")) {
            echo "selected";
        }
        echo " value=\"planned\">Planned</option>
                <option ";
        // line 10
        if (isset($context["project_status"])) { $_project_status_ = $context["project_status"]; } else { $_project_status_ = null; }
        if (($_project_status_ == "pending")) {
            echo "selected";
        }
        echo " value=\"pending\">Pending</option>
                <option ";
        // line 11
        if (isset($context["project_status"])) { $_project_status_ = $context["project_status"]; } else { $_project_status_ = null; }
        if (($_project_status_ == "failed")) {
            echo "selected";
        }
        echo " value=\"failed\">Failed</option>
                <option ";
        // line 12
        if (isset($context["project_status"])) { $_project_status_ = $context["project_status"]; } else { $_project_status_ = null; }
        if (($_project_status_ == "completed")) {
            echo "selected";
        }
        echo " value=\"completed\">Completed</option>
            </select>
        </td>
    </tr>
    <tr>
        <th style=''><label for='Project Duration'>Project Duration *</label></th>
        <td><input class='widefat' name='txt_duration' id='txt_duration' type='text' value='";
        // line 18
        if (isset($context["project_duration"])) { $_project_duration_ = $context["project_duration"]; } else { $_project_duration_ = null; }
        echo twig_escape_filter($this->env, $_project_duration_, "html", null, true);
        echo "' /></td>
    </tr>
    <tr>
        <th style=''><label for='Project URL'>Project URL</label></th>
        <td>
            
            <input class='widefat' name='txt_url' id='txt_url' type='text' value='";
        // line 24
        if (isset($context["project_url"])) { $_project_url_ = $context["project_url"]; } else { $_project_url_ = null; }
        echo twig_escape_filter($this->env, $_project_url_, "html", null, true);
        echo "'  /></td>
    </tr>    
    <tr>
        <th style=''><label for='Download URL'>Download URL</label></th>
        <td><input class='widefat' name='txt_download_url' id='txt_download_url' type='text'  value='";
        // line 28
        if (isset($context["project_download_url"])) { $_project_download_url_ = $context["project_download_url"]; } else { $_project_download_url_ = null; }
        echo twig_escape_filter($this->env, $_project_download_url_, "html", null, true);
        echo "' /></td>
    </tr>
    
</table>
";
    }

    public function getTemplateName()
    {
        return "project_meta.html";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  89 => 28,  81 => 24,  71 => 18,  59 => 12,  52 => 11,  45 => 10,  38 => 9,  31 => 8,  19 => 1,);
    }
}
