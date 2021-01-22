# Tiles Factory

## Description

An OctoberCMS plugin for easy creation and usage of cards (tiles). The plugin supports 'out of the box' the TailwindCSS-based cards format used by [FateFactory](https://fatefactory.com?target=_blank), so that you can use FF's visual card browser, pick your favourite card, and use it in your website.

Plugin contains both component and snippet for use either in 'CMS' pages or 'Static Pages'.

## Documentation

### Installation

Install plugin as usual:
- using the OctoberCMS Marketplace or
- get it from GitHub

If you choose manual install (e.g. using GitHub), make sure to intialize the database tables using the cli command `php artisan october:up`. This will create two tables.

### Configuration and usage

#### Sections

![Backend - Tiles component main interface](https://fatefactory.com/storage/app/media/plugins/tiles/sections-list.png)

Start by adding at least one new section. In the section editor, provide at least the name, and turn the visibility of the section on.

A section constitutes a category of cards to be displayed on a specific page. Using the provided component, you can select a single section of tiles to be displayed. Each section can have a default display style, but it can be overriden in the CMS or StaticPage configuration, so that you can have the same dataset displayed using different styles on different pages.

![Backend - create new section of tiles](https://fatefactory.com/storage/app/media/plugins/tiles/sections-create-edit-tab1.png)

The section editor has three tabs:
- The MAIN tab lets you define the basic (required) settings, and you can start adding new cards directly inside the section editor.
- The LAYOUT tab has a code editor, empty by default, typically used with the [FateFactory.com](https://fatefactory.com?target=_blank) to paste any card TWIG code from the FateFactory

![Backend - create - layout tab](https://fatefactory.com/storage/app/media/plugins/tiles/sections-create-edit-tab2.png)

- The LABELS tab should be ignored for now - in future versions, it will allow you to override the default Card fields with custom ones.

Then create one or more individual cards in the "cards" section.

#### Section templates

Card sections can use different layout templates.

A default template has been defined in the plugin (`plugins\cjkpl\tiles\components\section\tileset1.htm`), you can also add new templates into your theme as partials located in `THEME-FOLDER/partials/tiles/`. For example, if you copy the default `tileset1.htm` into `THEME-FOLDER/partials/tiles/homepage.htm`, it will be listed in the component Layouts selector as 'homepage.htm'.

Use either `tileset1.htm` or any TWIG card template from FateFactory.com as an inspiration for your new card layouts.

#### Cards

Card editor contains a set of typical fields used in Cards or Tiles. They are divided into tabs.
Individual fields are mapped onto the TWIG variables defined in the section templates. Twig field names are listed below each field to help you work with the card templates.

- Misc - contains the most frequently used fields, like Title (`{{ card.title }}`), background image (`{{ card.image }}`)
- Person - fields used in personal cards, like name, status, photo, etc.
- Tags and URLs - if the card is clickable, the URL field should be completed; If the card layout supports tags, you can add them here.
- Custom sets - this part is currently under development

#### Visibility and SEO

Note: cjkpl.seo is not yet on the OctoberCMS Market. The SEO functionality can be ignored for now.

A card will be shown if ```is_visible``` is true, and it's parent section ```is_visible``` is true.
A card will respond to ```cjkpl.seo.prepare``` event if ```is_seo``` is true (which should be by default)

Card's tags are by default used as SEO keywords - they are appended to the page's default keywords.

#### Example

See in the example below, how a TWIG template from FateFactory allows automatic mapping of the Tag field onto a card definition:
![Card field mapping](https://fatefactory.com/storage/app/media/plugins/tiles/ff-card-mapping.png)

#### Using in CMS and static pages

![Backend - add component (or snippet) to a page](https://fatefactory.com/storage/app/media/plugins/tiles/static-add-page.png)

The screenshot above shows the dialog inside a snippet added to a StaticPage. A similar setup can be done using plain CMS pages, instead of a snippet, use a component.

The Dialog for configuring a "Section of Cards" contains the following options:
- Columns: the number of columns in one row. Most card definitions at FateFactory nicely respond to browser window size changes.
- Layout: defines the card template to use. By default, it will use `plugins\cjkpl\tiles\components\section\tileset1.htm`. Note, if you decide to fork the component in the backend page editor, it will clone just the `plugins\cjkpl\tiles\components\section\default.htm`, which is not enough - most likely will want to edit the `tileset1.htm` as well, as this is where the cards are defined. The `default.htm` merely decides which layout to load: a default one, an inline one (i.e. one defined in the "Section Layout" form editor), or one of the partials defined in `THEME-FOLDER/partials/tiles/`
- Language filter: author's personal requirements called for an easy way to add cards in multiple languages (also in one section), and this option allows you to select for display only cards in a specific language.
- Section: decides which of the sections defined in the Tiles Section editor in the backend should be displayed. You can only display one section at a time.

#### Changelog

* 1.0.10 (2020-09-22). Renamed tags_url_patter to tags_url column and turned it into a repeater. This allows generating tags with unique urls, e.g. ```{% for tag in xtags %} {{ tag.label }} {{ tag.url }} {{ tag.target }} {% endfor %}```
