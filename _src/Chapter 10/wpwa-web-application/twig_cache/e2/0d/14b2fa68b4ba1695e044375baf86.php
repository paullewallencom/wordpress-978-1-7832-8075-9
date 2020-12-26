<?php

/* book_meta.html */
class __TwigTemplate_e20d14b2fa68b4ba1695e044375baf86 extends Twig_Template
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
        echo "<input type=\"hidden\" name=\"book_meta_nonce\" value=\"";
        if (isset($context["book_meta_nonce"])) { $_book_meta_nonce_ = $context["book_meta_nonce"]; } else { $_book_meta_nonce_ = null; }
        echo twig_escape_filter($this->env, $_book_meta_nonce_, "html", null, true);
        echo "\" />

<table class=\"form-table\">
    <tr>
        <th style=''><label for='Book Pages'>Book Pages *</label></th>
        <td><input class='widefat' name='txt_book_pages' id='txt_book_pages' type='text' value='";
        // line 6
        if (isset($context["book_pages"])) { $_book_pages_ = $context["book_pages"]; } else { $_book_pages_ = null; }
        echo twig_escape_filter($this->env, $_book_pages_, "html", null, true);
        echo "' /></td>
    </tr>
    <tr>
        <th style=''><label for='Book URL'>Book URL</label></th>
        <td>
            
            <input class='widefat' name='txt_book_url' id='txt_book_url' type='text' value='";
        // line 12
        if (isset($context["book_url"])) { $_book_url_ = $context["book_url"]; } else { $_book_url_ = null; }
        echo twig_escape_filter($this->env, $_book_url_, "html", null, true);
        echo "'  /></td>
    </tr>    
    <tr>
        <th style=''><label for='Book Publisher'>Book Publisher</label></th>
        <td><input class='widefat' name='txt_book_publisher' id='txt_book_publisher' type='text'  value='";
        // line 16
        if (isset($context["book_publisher"])) { $_book_publisher_ = $context["book_publisher"]; } else { $_book_publisher_ = null; }
        echo twig_escape_filter($this->env, $_book_publisher_, "html", null, true);
        echo "' /></td>
    </tr>
    
</table>
";
    }

    public function getTemplateName()
    {
        return "book_meta.html";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  47 => 16,  39 => 12,  29 => 6,  19 => 1,);
    }
}
