<?php

/* page/index */
class __TwigTemplate_3bb1c9b441ed0587a08a32c2880e74bc extends Twig_Template
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
<div data-theme=\"c\" data-role=\"header\">
\t<h3>
\t\t";
        // line 4
        echo twig_escape_filter($this->env, (isset($context["label"]) ? $context["label"] : null), "html", null, true);
        echo "
\t</h3>
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
        return array (  24 => 4,  19 => 1,);
    }
}
