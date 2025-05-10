/**
 * Copyright (c) Christoph M. Becker
 *
 * This file is part of Pdeditor_XH.
 *
 * Pdeditor_XH is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * Pdeditor_XH is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with Pdeditor_XH.  If not, see <http://www.gnu.org/licenses/>.
 */

/*jslint browser: true, maxlen: 80 */

(function () {
    "use strict";

    /**
     * Gets elements by class name.
     *
     * @param {Element} element
     * @param {string}  className
     *
     * @returns {Array}
     */
    function getByClassName(element, className) {
        var regex, children, i, len, child, result;

        result = [];
        regex = new RegExp("(^|\\s)" + className + "(\\s|$)");
        children = element.getElementsByTagName("*");
        for (i = 0, len = children.length; i < len; i += 1) {
            child = children[i];
            if (regex.test(child.className)) {
                result.push(child);
            }
        }
        return result;
    }

    /**
     * Calls a callback for each element.
     *
     * @param {Array}    elements
     * @param {Function} func
     *
     * @returns {undefined}
     */
    function forEach(elements, func) {
        var i, len;

        for (i = 0, len = elements.length; i < len; i += 1) {
            func(elements[i], i);
        }
    }

    /**
     * Registers an event listener.
     *
     * @param {EventTarget}   target
     * @param {string}        type
     * @param {EventListener} listener
     *
     * @returns {undefined}
     */
    function on(target, type, listener) {
        if (typeof target.addEventListener !== "undefined") {
            target.addEventListener(type, listener, false);
        } else if (typeof target.attachEvent !== "undefined") {
            target.attachEvent("on" + type, listener);
        }
    }

    /**
     * Initializes the page data editor.
     *
     * @returns {undefined}
     */
    function init() {
        var attribute, attributes, select, headings;

        /*
         * Returns the requested page data attribute from the query string.
         *
         * @returns {string}
         */
        function getRequestedAttribute() {
            var parts, i, pair;

            parts = location.search.substr(1).split("&");
            for (i = 0; i < parts.length; i += 1) {
                pair = parts[i].split("=");
                if (pair[0] === "pdeditor_attr") {
                    return pair[1];
                }
            }
            return "url";
        }

        /**
         * Adds an option to the select element.
         *
         * @param {HTMLLiElement} item
         *
         * @returns {undefined}
         */
        function addOption(item) {
            var option, anchor, text;

            anchor = item.getElementsByTagName("a")[0];
            option = document.createElement("option");
            text = anchor.textContent || anchor.innerText;
            if (text === attribute) {
                option.selected = true;
            }
            option.appendChild(document.createTextNode(text));
            option.value = anchor.href;
            select.appendChild(option);
        }

        attribute = getRequestedAttribute();
        select = document.createElement("select");
        attributes = document.getElementById("pdeditor_attr").
                getElementsByTagName("li");
        forEach(attributes, addOption);
        on(select, "change", function (event) {
            location.href = (event.target || event.srcElement).value;
        });
        document.getElementById("pdeditor_attr").parentNode.replaceChild(
            select,
            document.getElementById("pdeditor_attr")
        );
        headings = getByClassName(document, "pdeditor_heading");
        forEach(headings, function (el) {
            el.parentNode.removeChild(el);
        });
        document.getElementById("pdeditor_delete").style.display =
                "inline-block";
    }

    on(window, "load", init);
}());
