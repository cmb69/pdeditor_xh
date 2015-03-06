/*!
 * Pdeditor_XH
 *
 * @author  Christoph M. Becker <cmbecker69@gmx.de>
 * @license GPL-3.0+
 */

/*jslint browser: true, maxlen: 80 */

if (typeof window.addEventListener !== "undefined") {
    window.addEventListener("load", function () {
        "use strict";

        var attribute, attributes, select, headings;

        /*
         * Returns the current page data attribute from the query string.
         *
         * @returns {string}
         */
        function getAttribute() {
            var parts, i, pair;

            parts = window.location.search.substr(1).split("&");
            for (i = 0; i < parts.length; i += 1) {
                pair = parts[i].split("=");
                if (pair[0] === "pdeditor_attr") {
                    return pair[1];
                }
            }
            return "url";
        }

        attribute = getAttribute();
        select = document.createElement("select");
        attributes = document.querySelectorAll("#pdeditor_attr > li");
        attributes = Array.prototype.slice.call(attributes);
        attributes = attributes.forEach(function (el) {
            var option, anchor;

            anchor = el.getElementsByTagName("a")[0];
            option = document.createElement("option");
            option.text = anchor.textContent;
            if (option.text === attribute) {
                option.selected = true;
            }
            option.value = anchor.href;
            select.appendChild(option);
        });
        select.addEventListener("change", function (event) {
            window.location = event.target.value;
        });
        document.getElementById("pdeditor_attr").parentNode.replaceChild(
            select,
            document.getElementById("pdeditor_attr")
        );
        headings = document.querySelectorAll(".pdeditor_heading");
        headings = Array.prototype.slice.call(headings);
        headings.forEach(function (el) {
            el.parentNode.removeChild(el);
        });
        document.getElementById("pdeditor_delete").style.display =
                "inline-block";
    });
}
