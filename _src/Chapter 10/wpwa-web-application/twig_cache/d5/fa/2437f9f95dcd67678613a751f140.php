<?php

/* article_meta.html */
class __TwigTemplate_d5fa2437f9f95dcd67678613a751f140 extends Twig_Template
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
        echo "<input type=\"hidden\" name=\"article_meta_nonce\" value=\"";
        if (isset($context["article_meta_nonce"])) { $_article_meta_nonce_ = $context["article_meta_nonce"]; } else { $_article_meta_nonce_ = null; }
        echo twig_escape_filter($this->env, $_article_meta_nonce_, "html", null, true);
        echo "\" />

<table class=\"form-table\">
    <tr>
        <th style=''><label for='Article URL'>Article URL *</label></th>
        <td><input class='widefat' name='article_url' id='article_url' type='text' value='";
        // line 6
        if (isset($context["article_url"])) { $_article_url_ = $context["article_url"]; } else { $_article_url_ = null; }
        echo twig_escape_filter($this->env, $_article_url_, "html", null, true);
        echo "' /></td>
    </tr>    
</table>
";
    }

    public function getTemplateName()
    {
        return "article_meta.html";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  29 => 6,  19 => 1,);
    }
}
