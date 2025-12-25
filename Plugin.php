<?php

namespace TypechoPlugin\CuteSun;

use Typecho\Plugin\PluginInterface;
use Typecho\Widget\Helper\Form;
use Typecho\Widget\Helper\Form\Element\Radio;
use Widget\Options;

if (!defined('__TYPECHO_ROOT_DIR__')) {
    exit;
}

/**
 * A cute interactive widget plugin with multiple styles (Sun, Pixel Cat, Pixel Coffee, Kanji Badge).
 *
 * @package CuteSun
 * @author Sluke
 * @version 1.0.0
 * @link http://typecho.org
 */
class Plugin implements PluginInterface
{
    /**
     * Activate plugin
     */
    public static function activate()
    {
        \Typecho\Plugin::factory('Widget_Archive')->footer = __CLASS__ . '::render';
    }

    /**
     * Deactivate plugin
     */
    public static function deactivate()
    {
    }

    /**
     * Config panel
     *
     * @param Form $form
     */
    public static function config(Form $form)
    {
        $style = new Radio('style', [
            'sun' => _t('Cute Sun'),
            'cat' => _t('Pixel Cat (Color Cycling)'),
            'coffee' => _t('Pixel Coffee (Steaming/Empty)'),
            'badge' => _t('Kanji Badge (Character Cycling)')
        ], 'sun', _t('Widget Style'), _t('Choose the appearance of the widget.'));
        $form->addInput($style);
    }

    /**
     * Personal config panel
     *
     * @param Form $form
     */
    public static function personalConfig(Form $form)
    {
    }

    /**
     * Render the plugin
     *
     * @access public
     * @return void
     */
    public static function render()
    {
        $options = Options::alloc();
        $style = $options->plugin('CuteSun')->style;

        // Cleanup old styles fallback
        if (!in_array($style, ['sun', 'cat', 'coffee', 'badge'])) {
            $style = 'sun';
        }

        // Common Container
        $initialClass = '';
        if ($style === 'sun' || $style === 'coffee') {
            $initialClass = 'active';
        } elseif ($style === 'cat') {
            $initialClass = 'cat-orange';
        } elseif ($style === 'badge') {
            $initialClass = 'badge-kame';
        }

        echo '<div id="cute-widget-container" class="style-' . $style . ' ' . $initialClass . '" data-style="' . $style . '">';

        if ($style === 'sun') {
            self::renderSun();
        } elseif ($style === 'cat') {
            self::renderCat();
        } elseif ($style === 'coffee') {
            self::renderCoffee();
        } elseif ($style === 'badge') {
            self::renderBadge();
        }

        echo '</div>';

        self::renderScript($style);
        self::renderStyles($style);
    }

    private static function renderSun()
    {
        echo <<<HTML
    <div class="sun-face">
        <div class="eye left"></div>
        <div class="eye right"></div>
        <div class="mouth"></div>
        <div class="blush left"></div>
        <div class="blush right"></div>
    </div>
    <div class="sun-rays">
        <div class="ray"></div>
        <div class="ray"></div>
        <div class="ray"></div>
        <div class="ray"></div>
        <div class="ray"></div>
        <div class="ray"></div>
        <div class="ray"></div>
        <div class="ray"></div>
    </div>
HTML;
    }

    private static function renderCat()
    {
        echo <<<HTML
    <div class="pixel-cat"></div>
HTML;
    }

    private static function renderCoffee()
    {
        echo <<<HTML
    <div class="pixel-cup-container">
        <div class="pixel-cup"></div>
        <div class="pixel-steam s1"></div>
        <div class="pixel-steam s2"></div>
        <div class="pixel-steam s3"></div>
    </div>
HTML;
    }

    private static function renderBadge()
    {
        echo <<<HTML
    <div class="badge-inner">
        <span class="badge-text k">龟</span>
        <span class="badge-text j">界</span>
        <span class="badge-text w">悟</span>
        <span class="badge-text c">超</span>
    </div>
HTML;
    }

    private static function renderScript($style)
    {
        // Inject JS appropriate for the style
        if ($style === 'sun' || $style === 'coffee') {
            // Simple toggle
            echo <<<SCRIPT
<script>
document.addEventListener('DOMContentLoaded', function() {
    var widget = document.getElementById('cute-widget-container');
    if (widget) {
        widget.addEventListener('click', function() {
            this.classList.toggle('active');
        });
    }
});
</script>
SCRIPT;
        } elseif ($style === 'cat') {
            // Color cycling
            echo <<<SCRIPT
<script>
document.addEventListener('DOMContentLoaded', function() {
    var widget = document.getElementById('cute-widget-container');
    var colors = ['cat-orange', 'cat-yellow', 'cat-white', 'cat-black', 'cat-brown'];
    var currIndex = 0;
    
    if (widget) {
        widget.addEventListener('click', function() {
            // Find current color index
            for (var i = 0; i < colors.length; i++) {
                if (widget.classList.contains(colors[i])) {
                    currIndex = i;
                    widget.classList.remove(colors[i]);
                    break;
                }
            }
            
            // Next color
            currIndex = (currIndex + 1) % colors.length;
            widget.classList.add(colors[currIndex]);
        });
    }
});
</script>
SCRIPT;
        } elseif ($style === 'badge') {
            echo <<<SCRIPT
<script>
document.addEventListener('DOMContentLoaded', function() {
    var widget = document.getElementById('cute-widget-container');
    var states = ['badge-kame', 'badge-kai', 'badge-go', 'badge-super'];
    var currIndex = 0;
    
    if (widget) {
        widget.addEventListener('click', function() {
            for (var i = 0; i < states.length; i++) {
                if (widget.classList.contains(states[i])) {
                    currIndex = i;
                    widget.classList.remove(states[i]);
                    break;
                }
            }
            currIndex = (currIndex + 1) % states.length;
            widget.classList.add(states[currIndex]);
        });
    }
});
</script>
SCRIPT;
        }
    }

    private static function renderStyles($style)
    {
        echo '<style>';
        // Base container styles
        echo <<<CSS
#cute-widget-container {
    position: fixed;
    right: 30px;
    bottom: 30px;
    width: 60px;
    height: 60px;
    cursor: pointer;
    z-index: 9999;
    user-select: none;
    transition: transform 0.2s;
}
#cute-widget-container:active {
    transform: scale(0.9);
}
CSS;

        if ($style === 'sun') {
            self::renderSunCSS();
        } elseif ($style === 'cat') {
            self::renderCatCSS();
        } elseif ($style === 'coffee') {
            self::renderCoffeeCSS();
        } elseif ($style === 'badge') {
            self::renderBadgeCSS();
        }

        echo '</style>';
    }

    private static function renderSunCSS()
    {
        echo <<<CSS
.sun-face {
    position: absolute;
    top: 50%;
    left: 50%;
    width: 40px;
    height: 40px;
    background-color: #ffd700;
    border: 2px solid #000;
    border-radius: 50%;
    transform: translate(-50%, -50%);
    z-index: 2;
    box-sizing: border-box;
    overflow: hidden;
    transition: background-color 0.3s;
}
#cute-widget-container.active .sun-face {
    background-color: #ffeb3b;
    box-shadow: 0 0 15px rgba(255, 235, 59, 0.8);
}
.eye {
    position: absolute;
    top: 12px;
    width: 4px;
    height: 4px;
    background-color: #000;
    border-radius: 50%;
    transition: height 0.2s, top 0.2s;
}
.eye.left { left: 10px; }
.eye.right { right: 10px; }
#cute-widget-container.active .eye {
    height: 2px;
    top: 14px;
    width: 6px;
    border-radius: 50% 50% 0 0 / 100% 100% 0 0;
    background-color: transparent;
    border-top: 2px solid #000;
}
.blush {
    position: absolute;
    top: 20px;
    width: 6px;
    height: 3px;
    background-color: #ffab91;
    border-radius: 50%;
    opacity: 0;
    transition: opacity 0.3s;
}
.blush.left { left: 6px; }
.blush.right { right: 6px; }
#cute-widget-container.active .blush { opacity: 1; }
.mouth {
    position: absolute;
    bottom: 10px;
    left: 50%;
    width: 8px;
    height: 2px;
    background-color: #000;
    border-radius: 2px;
    transform: translateX(-50%);
    transition: all 0.3s;
}
#cute-widget-container.active .mouth {
    bottom: 6px;
    width: 14px;
    height: 8px;
    background-color: transparent;
    border-bottom: 2px solid #000;
    border-radius: 0 0 50% 50%;
}
.sun-rays {
    position: absolute;
    top: 50%;
    left: 50%;
    width: 100%;
    height: 100%;
    transform: translate(-50%, -50%);
    z-index: 1;
    opacity: 0;
    transition: opacity 0.3s;
    pointer-events: none;
}
#cute-widget-container.active .sun-rays { opacity: 1; }
.ray {
    position: absolute;
    top: 50%;
    left: 50%;
    width: 2px;
    height: 10px;
    background-color: #000;
    margin-top: -5px;
    margin-left: -1px;
}
.ray:nth-child(1) { transform: rotate(0deg) translateY(-31px); }
.ray:nth-child(2) { transform: rotate(45deg) translateY(-31px); }
.ray:nth-child(3) { transform: rotate(90deg) translateY(-31px); }
.ray:nth-child(4) { transform: rotate(135deg) translateY(-31px); }
.ray:nth-child(5) { transform: rotate(180deg) translateY(-31px); }
.ray:nth-child(6) { transform: rotate(225deg) translateY(-31px); }
.ray:nth-child(7) { transform: rotate(270deg) translateY(-31px); }
.ray:nth-child(8) { transform: rotate(315deg) translateY(-31px); }
CSS;
    }

    private static function renderCatCSS()
    {
        echo <<<CSS
.pixel-cat {
    position: absolute;
    top: 50%;
    left: 50%;
    width: 4px;
    height: 4px;
    background-color: transparent;
    transform: translate(-50%, -50%);
    box-shadow: 
        -12px -20px 0 var(--cat-color), -8px -20px 0 var(--cat-color), 8px -20px 0 var(--cat-color), 12px -20px 0 var(--cat-color),
        -16px -16px 0 var(--cat-color), -12px -16px 0 var(--cat-color), -8px -16px 0 var(--cat-color), -4px -16px 0 var(--cat-color),
        4px -16px 0 var(--cat-color), 8px -16px 0 var(--cat-color), 12px -16px 0 var(--cat-color), 16px -16px 0 var(--cat-color),
        -20px -12px 0 var(--cat-color), -16px -12px 0 var(--cat-color), -12px -12px 0 var(--cat-color), -8px -12px 0 var(--cat-color), -4px -12px 0 var(--cat-color), 0px -12px 0 var(--cat-color),
        4px -12px 0 var(--cat-color), 8px -12px 0 var(--cat-color), 12px -12px 0 var(--cat-color), 16px -12px 0 var(--cat-color), 20px -12px 0 var(--cat-color),
        -20px -8px 0 var(--cat-color), -16px -8px 0 var(--cat-color), -12px -8px 0 var(--cat-color), -8px -8px 0 var(--cat-color), -4px -8px 0 var(--cat-color), 0px -8px 0 var(--cat-color),
        4px -8px 0 var(--cat-color), 8px -8px 0 var(--cat-color), 12px -8px 0 var(--cat-color), 16px -8px 0 var(--cat-color), 20px -8px 0 var(--cat-color),
        -24px -4px 0 var(--cat-color), -20px -4px 0 var(--cat-color), -16px -4px 0 var(--cat-color), -12px -4px 0 var(--cat-color), -8px -4px 0 var(--cat-color), -4px -4px 0 var(--cat-color), 0px -4px 0 var(--cat-color),
        4px -4px 0 var(--cat-color), 8px -4px 0 var(--cat-color), 12px -4px 0 var(--cat-color), 16px -4px 0 var(--cat-color), 20px -4px 0 var(--cat-color), 24px -4px 0 var(--cat-color),
        -24px 0px 0 var(--cat-color), -8px 0px 0 var(--cat-color), -4px 0px 0 var(--cat-color), 0px 0px 0 var(--cat-color), 4px 0px 0 var(--cat-color), 8px 0px 0 var(--cat-color), 24px 0px 0 var(--cat-color),
        -24px 4px 0 var(--cat-color), -8px 4px 0 var(--cat-color), -4px 4px 0 var(--cat-color), 0px 4px 0 var(--cat-color), 4px 4px 0 var(--cat-color), 8px 4px 0 var(--cat-color), 24px 4px 0 var(--cat-color),
        -24px 8px 0 var(--cat-color), -20px 8px 0 var(--cat-color), -16px 8px 0 var(--cat-color), -12px 8px 0 var(--cat-color), -8px 8px 0 var(--cat-color), -4px 8px 0 var(--cat-color), 0px 8px 0 var(--cat-color),
        4px 8px 0 var(--cat-color), 8px 8px 0 var(--cat-color), 12px 8px 0 var(--cat-color), 16px 8px 0 var(--cat-color), 20px 8px 0 var(--cat-color), 24px 8px 0 var(--cat-color),
        -20px 12px 0 var(--cat-color), -16px 12px 0 var(--cat-color), -12px 12px 0 var(--cat-color), -8px 12px 0 var(--cat-color), -4px 12px 0 var(--cat-color), 0px 12px 0 var(--cat-color),
        4px 12px 0 var(--cat-color), 8px 12px 0 var(--cat-color), 12px 12px 0 var(--cat-color), 16px 12px 0 var(--cat-color), 20px 12px 0 var(--cat-color),
        -16px 16px 0 var(--cat-color), -12px 16px 0 var(--cat-color), -8px 16px 0 var(--cat-color), -4px 16px 0 var(--cat-color), 0px 16px 0 var(--cat-color),
        4px 16px 0 var(--cat-color), 8px 16px 0 var(--cat-color), 12px 16px 0 var(--cat-color), 16px 16px 0 var(--cat-color),
        -12px 20px 0 var(--cat-color), -8px 20px 0 var(--cat-color), -4px 20px 0 var(--cat-color), 0px 20px 0 var(--cat-color),
        4px 20px 0 var(--cat-color), 8px 20px 0 var(--cat-color), 12px 20px 0 var(--cat-color);
    transition: color 0.3s;
    color: var(--cat-color);
}
.cat-orange { --cat-color: #ff9800; }
.cat-yellow { --cat-color: #ffeb3b; }
.cat-white  { --cat-color: #e0e0e0; }
.cat-black  { --cat-color: #212121; }
.cat-brown  { --cat-color: #795548; }
CSS;
    }

    private static function renderCoffeeCSS()
    {
        // 1 pixel = 4px
        echo <<<CSS
.pixel-cup-container {
    position: absolute;
    top: 50%;
    left: 50%;
    width: 60px;
    height: 60px;
    transform: translate(-50%, -50%);
}

.pixel-cup {
    position: absolute;
    top: 50%;
    left: 50%;
    width: 4px;
    height: 4px;
    background-color: transparent;
    transform: translate(-50%, -10%); /* Shifted down a bit */
    color: #333; /* Darker Cup Stroke for readability */
    
    /* 
     Cup Shape (12 wide x 8 high) + Handle
     Contents inside
    */
    box-shadow:
        /* Rim */
        -16px -8px 0, -12px -8px 0, -8px -8px 0, -4px -8px 0, 0px -8px 0, 4px -8px 0, 8px -8px 0, 12px -8px 0,
        /* Left Wall */
        -16px -4px 0, -16px 0px 0, -16px 4px 0, -16px 8px 0,
        /* Right Wall */
        12px -4px 0, 12px 0px 0, 12px 4px 0, 12px 8px 0,
        /* Bottom */
        -12px 12px 0, -8px 12px 0, -4px 12px 0, 0px 12px 0, 4px 12px 0, 8px 12px 0,
        /* Handle */
        16px -4px 0, 20px -4px 0, 20px 0px 0, 20px 4px 0, 16px 4px 0;
}

/* Contents - Only when active (Full) */
#cute-widget-container.active .pixel-cup {
    box-shadow:
        /* Rim */
        -16px -8px 0, -12px -8px 0, -8px -8px 0, -4px -8px 0, 0px -8px 0, 4px -8px 0, 8px -8px 0, 12px -8px 0,
        /* Left Wall */
        -16px -4px 0, -16px 0px 0, -16px 4px 0, -16px 8px 0,
        /* Right Wall */
        12px -4px 0, 12px 0px 0, 12px 4px 0, 12px 8px 0,
        /* Bottom */
        -12px 12px 0, -8px 12px 0, -4px 12px 0, 0px 12px 0, 4px 12px 0, 8px 12px 0,
        /* Handle */
        16px -4px 0, 20px -4px 0, 20px 0px 0, 20px 4px 0, 16px 4px 0,
        
        /* COFFEE FILL (Brown: #6d4c41) */
        -12px -4px 0 #6d4c41, -8px -4px 0 #6d4c41, -4px -4px 0 #6d4c41, 0px -4px 0 #6d4c41, 4px -4px 0 #6d4c41, 8px -4px 0 #6d4c41,
        -12px 0px 0 #6d4c41, -8px 0px 0 #6d4c41, -4px 0px 0 #6d4c41, 0px 0px 0 #6d4c41, 4px 0px 0 #6d4c41, 8px 0px 0 #6d4c41,
        -12px 4px 0 #6d4c41, -8px 4px 0 #6d4c41, -4px 4px 0 #6d4c41, 0px 4px 0 #6d4c41, 4px 4px 0 #6d4c41, 8px 4px 0 #6d4c41,
        -12px 8px 0 #6d4c41, -8px 8px 0 #6d4c41, -4px 8px 0 #6d4c41, 0px 8px 0 #6d4c41, 4px 8px 0 #6d4c41, 8px 8px 0 #6d4c41;
}

/* Steam */
.pixel-steam {
    position: absolute;
    width: 4px;
    height: 4px;
    background-color: #ccc;
    opacity: 0;
}
#cute-widget-container.active .pixel-steam {
    animation: rise 1.5s infinite linear;
}

.pixel-steam.s1 { left: 18px; top: 15px; animation-delay: 0s; }
.pixel-steam.s2 { left: 28px; top: 18px; animation-delay: 0.5s; }
.pixel-steam.s3 { left: 38px; top: 14px; animation-delay: 1.0s; }

@keyframes rise {
    0% { transform: translateY(0); opacity: 0; }
    20% { opacity: 0.8; }
    80% { opacity: 0.4; }
    100% { transform: translateY(-16px); opacity: 0; }
}
CSS;
    }

    private static function renderBadgeCSS()
    {
        echo <<<CSS
.badge-inner {
    position: absolute;
    top: 50%;
    left: 50%;
    width: 50px;
    height: 50px;
    background-color: #fff;
    border: 4px solid var(--badge-color, #333);
    border-radius: 50%;
    transform: translate(-50%, -50%);
    display: flex;
    align-items: center;
    justify-content: center;
    box-shadow: 0 4px 10px rgba(0,0,0,0.1);
    box-sizing: border-box;
    overflow: hidden;
    transition: transform 0.2s, border-color 0.3s;
}

.badge-text {
    display: none;
    font-size: 28px;
    font-weight: 900;
    color: var(--badge-color, #333);
    font-family: "Kaiti", "STKaiti", "PingFang SC", "Microsoft YaHei", serif;
    line-height: 1;
    transition: color 0.3s;
}

/* Visibility based on class and Color Sync */
.badge-kame { --badge-color: #333; }
.badge-kai  { --badge-color: #e53935; } 
.badge-go   { --badge-color: #8e24aa; }
.badge-super { --badge-color: #ffb300; }

.badge-kame .badge-text.k { display: block; }
.badge-kai .badge-text.j { display: block; }
.badge-go .badge-text.w { display: block; }
.badge-super .badge-text.c { display: block; }

/* Scale effect on click */
#cute-widget-container:active .badge-inner {
    transform: translate(-50%, -50%) scale(0.9);
}
CSS;
    }
}
