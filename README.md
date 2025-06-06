# Pdeditor_XH

Pdeditor_XH facilitates viewing and editing the page data in the back-end
of CMSimple_XH.  It is meant as an alternative to editing the pagedata
sections in content.htm manually, so it should be *used by experienced users only*,
who know exactly what they are doing.  It is *not a tool for unexperienced users*!
These should manipulate the pagedata via the interfaces provided by the respective
plugins only (often a tab above the editor).

- [Requirements](#requirements)
- [Download](#download)
- [Installation](#installation)
- [Settings](#settings)
- [Usage](#usage)
- [Troubleshooting](#troubleshooting)
- [License](#license)
- [Credits](#credits)

## Requirements

Pdeditor_XH is a plugin for [CMSimple_XH](https://cmsimple-xh.org/).
It requires CMSimple_XH ≥ 1.7.0 and PHP ≥ 7.1.0.
Pdeditor_XH also requires [Plib_XH](https://github.com/cmb69/plib_xh) ≥ 1.8;
if that is not already installed (see *Settings*→*Info*),
get the [lastest release](https://github.com/cmb69/plib_xh/releases/latest),
and install it.

## Download

The [lastest release](https://github.com/cmb69/pdeditor_xh/releases/latest)
is available for download on Github.

## Installation

The installation is done as with many other CMSimple_XH plugins.

1. Backup the data on your server.
1. Unzip the distribution on your computer.
1. Upload the whole folder `pdeditor/` to your server into the `plugins/`
   folder of CMSimple_XH.
1. Set write permissions to the subfolder `css/` and `languages/`.
1. Browse to `Plugins` → `Pdeditor` in the back-end,
   and check whether all requirements are fulfilled.

## Settings

The configuration of the plugin is done as with many other CMSimple_XH plugins
in the back-end of the Website.  Go to `Plugins` → `Pdeditor`.

<!-- You can change the default settings of Pdeditor_XH under `Config`.  Hints for
the options will be displayed when hovering over the help icon with your mouse.-->

Localization is done under `Language`.  You can translate the character
strings to your own language if there is no appropriate language file available,
or customize them according to your needs.

The look of Pdeditor_XH can be customized under `Stylesheet`.

## Usage

Pdeditor_XH is used exclusively from the back-end.
Open it via `Plugins` → `Pdeditor` → `Pagedata`.

Directly below the plugin menu you find the attribute selectbox and a button
to delete the selected attribute with all its values from the pagedata.
Note that deleting an attribute may cause malfunctions of the system.
You may be better off using only the page data cleanup feature
(`Settings` → `Page Data`) of CMSimple_XH.

After selecting an attribute you can view and edit the values of this
attribute of all pages.

## Troubleshooting

Report bugs and ask for support either on
[Github](https://github.com/cmb69/pdeditor_xh/issues)
or in the [CMSimple_XH Forum](https://cmsimpleforum.com/).

## License

Pdeditor_XH is free software: you can redistribute it and/or modify it
under the terms of the GNU General Public License as published
by the Free Software Foundation, either version 3 of the License,
or (at your option) any later version.

Pdeditor_XH is distributed in the hope that it will be useful,
but without any warranty; without even the implied warranty of merchantibility
or fitness for a particular purpose.
See the GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with Pdeditor_XH. If not, see https://www.gnu.org/licenses/.

Copyright © Christoph M. Becker

## Credits

The plugin icon is designed by [schollidesign](https://schollidesign.deviantart.com/).
Many thanks for publishing this icon under GPL.

Many thanks to the community at the
[CMSimple_XH Forum](https://www.cmsimpleforum.com/)
for tips, suggestions and testing.

And last but not least many thanks to [Peter Harteg](https://www.harteg.dk/),
the “father” of CMSimple, and all developers of [CMSimple_XH](https://www.cmsimple-xh.org/)
without whom this amazing CMS would not exist.
