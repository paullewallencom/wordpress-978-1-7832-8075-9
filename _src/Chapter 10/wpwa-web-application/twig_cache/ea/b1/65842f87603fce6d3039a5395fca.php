<?php

/* service_meta.html */
class __TwigTemplate_eab165842f87603fce6d3039a5395fca extends Twig_Template
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
        echo "<input type=\"hidden\" name=\"service_meta_nonce\" value=\"";
        if (isset($context["service_meta_nonce"])) { $_service_meta_nonce_ = $context["service_meta_nonce"]; } else { $_service_meta_nonce_ = null; }
        echo twig_escape_filter($this->env, $_service_meta_nonce_, "html", null, true);
        echo "\" />

<table class=\"form-table\">
<tr>
        <th style=''><label for='Service availability'>Service availability *</label></th>
        <td>
            <select class='widefat' name=\"service_availability\" id=\"service_availability\">
                <option ";
        // line 8
        if (isset($context["service_availability"])) { $_service_availability_ = $context["service_availability"]; } else { $_service_availability_ = null; }
        if (($_service_availability_ == 0)) {
            echo "selected";
        }
        echo " value=\"0\">Select</option>
                <option ";
        // line 9
        if (isset($context["service_availability"])) { $_service_availability_ = $context["service_availability"]; } else { $_service_availability_ = null; }
        if (($_service_availability_ == "av")) {
            echo "selected";
        }
        echo " value=\"av\">Available</option>
                <option ";
        // line 10
        if (isset($context["service_availability"])) { $_service_availability_ = $context["service_availability"]; } else { $_service_availability_ = null; }
        if (($_service_availability_ == "nav")) {
            echo "selected";
        }
        echo " value=\"nav\">Not available</option>
            </select>
        </td>
    </tr>
    <tr>
        <th style=''><label for='Service Price Type'>Service Prize Type </label></th>
        <td>
            <select class='widefat' name=\"service_price_type\" id=\"service_price_type\">
                <option ";
        // line 18
        if (isset($context["service_price_type"])) { $_service_price_type_ = $context["service_price_type"]; } else { $_service_price_type_ = null; }
        if (($_service_price_type_ == 0)) {
            echo "selected";
        }
        echo " value=\"0\">Select</option>
                <option ";
        // line 19
        if (isset($context["service_price_type"])) { $_service_price_type_ = $context["service_price_type"]; } else { $_service_price_type_ = null; }
        if (($_service_price_type_ == "hourly")) {
            echo "selected";
        }
        echo " value=\"hourly\">Hourly</option>
                <option ";
        // line 20
        if (isset($context["service_price_type"])) { $_service_price_type_ = $context["service_price_type"]; } else { $_service_price_type_ = null; }
        if (($_service_price_type_ == "fixed")) {
            echo "selected";
        }
        echo " value=\"fixed\">Fixed</option>
            </select>
        </td>
    </tr>
    <tr>
        <th style=''><label for='Price'>Price </label></th>
        <td><input class='widefat' name='service_price_value' id='service_price_value' type='text' value='";
        // line 26
        if (isset($context["service_price_value"])) { $_service_price_value_ = $context["service_price_value"]; } else { $_service_price_value_ = null; }
        echo twig_escape_filter($this->env, $_service_price_value_, "html", null, true);
        echo "' /></td>
    </tr>
    
    
</table>
";
    }

    public function getTemplateName()
    {
        return "service_meta.html";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  85 => 26,  73 => 20,  66 => 19,  59 => 18,  45 => 10,  38 => 9,  31 => 8,  19 => 1,);
    }
}
