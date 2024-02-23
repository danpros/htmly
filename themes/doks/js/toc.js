/*jslint
    white: true,
    browser: true,
    vars: true
*/

/**
 * Generates a table of contents for your document based on the headings
 *  present. Anchors are injected into the document and the
 *  entries in the table of contents are linked to them. The table of
 *  contents will be generated inside of the first element with the id `toc`.
 * @param {HTMLDOMDocument} documentRef Optional A reference to the document
 *  object. Defaults to `document`.
 * @author Matthew Christopher Kastor-Inare III
 * @version 20130726
 * @example
 * // call this after the page has loaded
 * htmlTableOfContents();
 */
 
 /**
 * Modified by @danpros
 * select only in #content
 * using the heading title as the slug IDs
 * insert the anchor inside the heading 
 * fix browser not scrolling to the hash
 */
function htmlTableOfContents (id) {
    var documentRef = document;
    var toc = documentRef.getElementById('toc');
    var headings = [].slice.call(documentRef.body.querySelectorAll('#content h1, #content h2, #content h3, #content h4, #content h5, #content h6'));
    headings.forEach(function (heading, index) {
        heading.setAttribute('id', heading.textContent.replace(/\s+/g, '-').toLowerCase() + id);

        var link = documentRef.createElement('a');
        link.setAttribute('href', '#' + heading.textContent.replace(/\s+/g, '-').toLowerCase() + id);
        link.textContent = heading.textContent;
        
        var div = documentRef.createElement('div');
        div.setAttribute('class', heading.tagName.toLowerCase() + '-toc');

        div.appendChild(link);
        toc.appendChild(div);
    });

    if (window.location.hash) {
        var hash = window.location.hash;
        scrollToHash(hash);
    }
}

// fix browser not scrolling to the hash
function scrollToHash (hash) {
    setTimeout(function() {
        hashtag = hash; 
        location.hash = '';
        location.hash = hashtag;
    }, 300);    
}

try {
     module.exports = htmlTableOfContents;
} catch (e) {
    // module.exports is not defined
}
