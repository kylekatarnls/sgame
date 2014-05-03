<?php

/* page/index */
class __TwigTemplate_489708c0be40a68d7123a639d4fa8bf3 extends Twig_Template
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
        echo (isset($context["title"]) ? $context["title"] : null);
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
        echo "<!--[if gte IE 9]><style type=\"text/css\"><span class=\"text\">.gradient{filter:none}</span></style><![endif]-->
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
\t\t\t\t";
        // line 25
        echo (isset($context["header"]) ? $context["header"] : null);
        echo "
\t\t\t\t<div data-role=\"content\">
\t\t\t\t\t<h1>";
        // line 27
        echo (isset($context["subtitle"]) ? $context["subtitle"] : null);
        echo "</h1>
<div>";
        // line 28
        echo twig_escape_filter($this->env, twig_date_format_filter($this->env, "now", "d/m/Y H:i", "Europe/Paris"), "html", null, true);
        echo "</div>
<p><span class=\"text\">Bonjour</span></p>
\t\t\t\t</div>
\t\t\t</div>
\t\t\t<div class=\"h-320 h-r\">
\t\t\t\t";
        // line 33
        $this->env->loadTemplate("bloc/hright")->display($context);
        // line 34
        echo "\t\t\t</div>
\t\t</div>
\t\t<div class=\"h-320 h-l\">
\t\t\t";
        // line 37
        $this->env->loadTemplate("bloc/hleft")->display($context);
        // line 38
        echo "\t\t</div>
\t</div>
\t";
        // line 40
        echo (isset($context["inline_script"]) ? $context["inline_script"] : null);
        echo "
\t";
        // line 41
        $context['_parent'] = (array) $context;
        $context['_seq'] = twig_ensure_traversable($this->getAttribute((isset($context["scripts"]) ? $context["scripts"] : null), "body"));
        foreach ($context['_seq'] as $context["_key"] => $context["script"]) {
            // line 42
            echo "<script type=\"text/javascript\" src=\"";
            echo twig_escape_filter($this->env, (isset($context["script"]) ? $context["script"] : null), "html", null, true);
            echo "\"></script>
\t";
        }
        $_parent = $context['_parent'];
        unset($context['_seq'], $context['_iterated'], $context['_key'], $context['script'], $context['_parent'], $context['loop']);
        $context = array_merge($_parent, array_intersect_key($context, $_parent));
        // line 44
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
        return array (  148 => 44,  139 => 42,  135 => 41,  131 => 40,  127 => 38,  125 => 37,  120 => 34,  118 => 33,  110 => 28,  106 => 27,  101 => 25,  93 => 20,  88 => 17,  79 => 15,  75 => 14,  72 => 13,  61 => 11,  57 => 10,  50 => 8,  40 => 7,  35 => 6,  31 => 5,  27 => 4,  22 => 2,  19 => 1,);
    }
}
