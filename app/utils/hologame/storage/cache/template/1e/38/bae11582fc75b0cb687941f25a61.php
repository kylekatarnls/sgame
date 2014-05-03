<?php

/* page/index */
class __TwigTemplate_1e38bae11582fc75b0cb687941f25a61 extends Twig_Template
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
        echo "
<div data-role=\"navbar\" data-iconpos=\"";
        // line 2
        echo twig_escape_filter($this->env, (isset($context["iconpos"]) ? $context["iconpos"] : null), "html", null, true);
        echo "\">
\t<ul>
\t\t<li>
\t\t\t<a href=\"";
        // line 5
        echo twig_escape_filter($this->env, (isset($context["href"]) ? $context["href"] : null), "html", null, true);
        echo "\" data-transition=\"fade\" data-theme=\"\" data-icon=\"\">
\t\t\t\t";
        // line 6
        echo (isset($context["label"]) ? $context["label"] : null);
        echo "
\t\t\t</a>
\t\t</li>
\t</ul>
</div>
";
    }

    public function getTemplateName()
    {
        return "page/index";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  32 => 6,  28 => 5,  22 => 2,  19 => 1,);
    }
}
