<?php

/* page/index */
class __TwigTemplate_f533c206f6b1ae1e00fa57c8a7706e4f extends Twig_Template
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
        echo "<!DOCTYPE HTML>
<html lang=\"";
        // line 2
        echo twig_escape_filter($this->env, (isset($context["lang"]) ? $context["lang"] : null), "html", null, true);
        echo "\">
<head>
\t<title>";
        // line 4
        echo twig_escape_filter($this->env, (isset($context["title"]) ? $context["title"] : null), "html", null, true);
        echo "</title>
\t";
        // line 5
        $context['_parent'] = (array) $context;
        $context['_seq'] = twig_ensure_traversable((isset($context["metas"]) ? $context["metas"] : null));
        foreach ($context['_seq'] as $context["_key"] => $context["meta"]) {
            // line 6
            echo "<meta";
            $context['_parent'] = (array) $context;
            $context['_seq'] = twig_ensure_traversable((isset($context["meta"]) ? $context["meta"] : null));
            foreach ($context['_seq'] as $context["_key"] => $context["attribute"]) {
                // line 7
                echo " ";
                echo twig_escape_filter($this->env, $this->getAttribute((isset($context["attribute"]) ? $context["attribute"] : null), "name"), "html", null, true);
                echo "=\"";
                echo twig_escape_filter($this->env, $this->getAttribute((isset($context["attribute"]) ? $context["attribute"] : null), "value"), "html", null, true);
                echo "\"";
            }
            $_parent = $context['_parent'];
            unset($context['_seq'], $context['_iterated'], $context['_key'], $context['attribute'], $context['_parent'], $context['loop']);
            $context = array_merge($_parent, array_intersect_key($context, $_parent));
            // line 8
            echo ">
\t";
        }
        $_parent = $context['_parent'];
        unset($context['_seq'], $context['_iterated'], $context['_key'], $context['meta'], $context['_parent'], $context['loop']);
        $context = array_merge($_parent, array_intersect_key($context, $_parent));
        // line 10
        $context['_parent'] = (array) $context;
        $context['_seq'] = twig_ensure_traversable((isset($context["styles"]) ? $context["styles"] : null));
        foreach ($context['_seq'] as $context["_key"] => $context["style"]) {
            // line 11
            echo "<link rel=\"stylesheet\" href=\"";
            echo twig_escape_filter($this->env, $this->getAttribute((isset($context["style"]) ? $context["style"] : null), "href"), "html", null, true);
            echo "\" media=\"";
            echo twig_escape_filter($this->env, $this->getAttribute((isset($context["style"]) ? $context["style"] : null), "media"), "html", null, true);
            echo "\">
\t";
        }
        $_parent = $context['_parent'];
        unset($context['_seq'], $context['_iterated'], $context['_key'], $context['style'], $context['_parent'], $context['loop']);
        $context = array_merge($_parent, array_intersect_key($context, $_parent));
        // line 13
        echo "<!--[if gte IE 9]><style type=\"text/css\">.gradient{filter:none}</style><![endif]-->
\t";
        // line 14
        $context['_parent'] = (array) $context;
        $context['_seq'] = twig_ensure_traversable($this->getAttribute((isset($context["scripts"]) ? $context["scripts"] : null), "head"));
        foreach ($context['_seq'] as $context["_key"] => $context["script"]) {
            // line 15
            echo "<script type=\"text/javascript\" src=\"";
            echo twig_escape_filter($this->env, (isset($context["script"]) ? $context["script"] : null), "html", null, true);
            echo "\"></script>
\t";
        }
        $_parent = $context['_parent'];
        unset($context['_seq'], $context['_iterated'], $context['_key'], $context['script'], $context['_parent'], $context['loop']);
        $context = array_merge($_parent, array_intersect_key($context, $_parent));
        // line 17
        echo "</head>
<body>
\t<h1>
\t\t";
        // line 20
        echo twig_escape_filter($this->env, (((isset($context["page_title"]) ? $context["page_title"] : null)) ? ((isset($context["page_title"]) ? $context["page_title"] : null)) : ((isset($context["title"]) ? $context["title"] : null))), "html", null, true);
        echo "
\t</h1>
\t<div class=\"h-960\">
\t\t<div class=\"h-640\">
\t\t\t<div class=\"h-320 h-c\">
\t\t\t\t<div data-theme=\"c\" data-role=\"header\">
\t\t\t\t\t<h3>
\t\t\t\t\t\tHologame
\t\t\t\t\t</h3>
\t\t\t\t</div>
\t\t\t\t<div data-role=\"content\">
\t\t\t\t\t";
        // line 31
        echo (isset($context["page"]) ? $context["page"] : null);
        echo "
\t\t\t\t</div>
\t\t\t</div>
\t\t\t<div class=\"h-320 h-r\">
\t\t\t\t";
        // line 35
        $this->env->loadTemplate("bloc/hright")->display($context);
        // line 36
        echo "\t\t\t</div>
\t\t</div>
\t\t<div class=\"h-320 h-l\">
\t\t\t";
        // line 39
        $this->env->loadTemplate("bloc/hleft")->display($context);
        // line 40
        echo "\t\t</div>
\t</div>
\t";
        // line 42
        echo (isset($context["inline_script"]) ? $context["inline_script"] : null);
        echo "
\t";
        // line 43
        $context['_parent'] = (array) $context;
        $context['_seq'] = twig_ensure_traversable($this->getAttribute((isset($context["scripts"]) ? $context["scripts"] : null), "body"));
        foreach ($context['_seq'] as $context["_key"] => $context["script"]) {
            // line 44
            echo "<script type=\"text/javascript\" src=\"";
            echo twig_escape_filter($this->env, (isset($context["script"]) ? $context["script"] : null), "html", null, true);
            echo "\"></script>
\t";
        }
        $_parent = $context['_parent'];
        unset($context['_seq'], $context['_iterated'], $context['_key'], $context['script'], $context['_parent'], $context['loop']);
        $context = array_merge($_parent, array_intersect_key($context, $_parent));
        // line 46
        echo "</body>
</html>";
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
        return array (  144 => 46,  135 => 44,  131 => 43,  127 => 42,  123 => 40,  121 => 39,  116 => 36,  114 => 35,  107 => 31,  93 => 20,  88 => 17,  79 => 15,  75 => 14,  72 => 13,  61 => 11,  57 => 10,  50 => 8,  40 => 7,  35 => 6,  31 => 5,  27 => 4,  22 => 2,  19 => 1,);
    }
}
