=== Visual Crossing Weather Forecast ===
Contributors: codextent
Donate link: 
Tags: weather shortcode, weather, weather widget, forecast, global, temp, local weather, local forecast, weather forecast, weather forecast API, weather forecast plugin
Requires at least: 5.6
Tested up to: 6.4.1
Stable tag: trunk
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Display beautifully formatted global weather forecasts in any WordPress website.

== Description ==

This plugin allows you to display beautifully formatted Weather Forecasts using a simple shortcode. You can insert this shortcode anywhere in your WordPress post or page. 

= Weather by visualcrossing.com =
The weather data is powered by [Visual Crossing Weather](https://www.visualcrossing.com/weather-data). 

Requires a free or paid key to access the data. 

* [Visual Crossing API Key](https://www.visualcrossing.com/weather-data-editions)


Once you have the API Key, you can save it in the WordPress admin panel under `'Settings' -> 'Weather Forecast Settings'`

## Shortcode
The plugin enabled a shortcode that allows you to display a weather forecast for any location worldwide:

* `[weather]`: display weather forecast using global settings.
* `[weather loc="London, UK" days="4"]`: display weather forecast overriding global settings using parameters.

#### Shortcode parameters

* **loc=''** *(String - Location or Address for which you want to fetch and display weather the forecast)*
* **days=7** *(Number - Number of days for which you want to display weather forecast)*
* **mode=''** *(string (can be “simple” or “d3”) - Used to indicate the format in which to display the weather forecast)*
* **title=''** *(String - Text you want to display above the weather forecast widget. Used only in “simple” mode)*
* **showtitle=''** *(string (can be “Yes” or “No”) Used to Hide or display the title)*
* **unit=''** *(string (can be “US” or “Metric”) - Unit system in which to display the weather forecast)*
* **conditions=''** *(string (can be “Yes” or “No”) - Used to Hide or display condition details.)*


== Installation ==

1. Add the plugin contents to the `/wp-content/plugins/` directory
1. Activate the plugin through the 'Plugins' menu in WordPress
1. Register for a  Visual Crossing [API Key](https://www.visualcrossing.com/weather-data-editions)
1. Add your API Key to the settings field in 'Settings' -> 'Weather Forecast Settings'
1. Use the `[weather]` shortcode to display the weather forecast anywhere on your WordPress site

The easiest shortcode setting is just: `[weather]`


== Screenshots ==

1. Setting Page
2. Insert shortcode in WordPress Page or Post
3. Simple Weather Forecast Demo
4. D3 Weather Forecast Demo

== Upgrade Notice ==



== Changelog ==

= 1.0.1 =
* Plugin compatibility tested.
= 1.0.0 =
* Initial load of the plugin.