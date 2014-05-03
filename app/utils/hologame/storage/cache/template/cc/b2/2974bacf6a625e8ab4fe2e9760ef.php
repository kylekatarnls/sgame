<?php

/* page/index */
class __TwigTemplate_ccb22974bacf6a625e8ab4fe2e9760ef extends Twig_Template
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
        echo twig_escape_filter($this->env, (isset($context["page_title"]) ? $context["page_title"] : null), "html", null, true);
        echo " - ";
        echo twig_escape_filter($this->env, (isset($context["main_title"]) ? $context["main_title"] : null), "html", null, true);
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
        return array (  19 => 1,);
    }
}
