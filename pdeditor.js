/**
 * JS of Pdeditor_XH.
 *
 * Copyright (c) 2012-2014 Christoph M. Becker (see license.txt)
 */

if (typeof window.addEventListener != "undefined") {
    window.addEventListener("load", function () {
        var attribute, attributes, select;

        attribute = location.search.split("=").pop();
        if (attribute == "plugin_text") {
            attribute = "url";
        }
        select = document.createElement("select");
        attributes = document.querySelectorAll("#pdeditor_attr > li");
        attributes = Array.prototype.slice.call(attributes);
        attributes = attributes.forEach(function (el) {
            var option, anchor;

            anchor = el.getElementsByTagName("a")[0];
            option = document.createElement("option");
            option.text = anchor.textContent;
            if (option.text == attribute) {
                option.selected = true;
            }
            option.value = anchor.href;
            select.appendChild(option);
        });
        select.addEventListener("change", function (event) {
            window.location = event.target.value;
        });
        document.getElementById("pdeditor_attr").parentNode.replaceChild(
                select, document.getElementById("pdeditor_attr"));
        headings = document.querySelectorAll("#pdeditor h4");
        headings = Array.prototype.slice.call(headings);
        headings.forEach(function (el) {
            el.parentNode.removeChild(el);
        });
        document.getElementById("pdeditor_delete").style.display =
                "inline-block";
    });
}
