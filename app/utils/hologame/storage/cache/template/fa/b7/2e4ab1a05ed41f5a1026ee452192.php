<?php

/* page/index */
class __TwigTemplate_fab72e4ab1a05ed41f5a1026ee452192 extends Twig_Template
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
        echo "<div data-role=\"collapsible-set\">
\t<div data-role=\"collapsible\" data-collapsed=\"";
        // line 2
        echo twig_escape_filter($this->env, (isset($context["collapsed"]) ? $context["collapsed"] : null), "html", null, true);
        echo "\">
\t\t<h3>
\t\t\t";
        // line 4
        echo (isset($context["label"]) ? $context["label"] : null);
        echo "
\t\t</h3>
\t\t";
        // line 6
        echo (isset($context["content"]) ? $context["content"] : null);
        echo "
\t</div>
</div>";
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
        return array (  32 => 6,  27 => 4,  22 => 2,  19 => 1,);
    }
}
