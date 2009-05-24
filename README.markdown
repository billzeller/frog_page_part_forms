About
-----

Plugin for the [Frog CMS][frog]. Defines and display custom forms the page parts of a page.

Motivation
----------

Frog has a simple but powerful mechanism to handle content. The content is organised in pages
and each page consists of at least one page part (called "body").
New page parts can easily added and accessed for content presentation, but only one page part can be
active at a time. A page part is a basic text field that can hold any kind of content (e.g. PHP script, HTML, etc.).

Page part forms fills the gap between the generic structure and a custom interface. With this plug-in a form can be
defined that is shown instead of the frog build in tab view. The form does not only contain basic text field, but allows e.g. selections and date fields.

Requirements
------------

- [jQuery plugin](http://github.com/tuupola/frog_jquery/tree/master)
- [page_part_metadata](http://github.com/them/frog_page_metadata/)

Install
-------

[Protect your plugins](http://forum.madebyfrog.com/topic/1233). Edit config.php and add the following line:

    define('IN_FROG', true);

Copy plugin files to _frog/plugins/page\_part\_forms/_ folder.

    cd frog/plugins/
    git clone git://github.com/them/frog_page_part_forms.git page_part_forms

Usage
-----

[frog]: http://www.madebyfrog.com/