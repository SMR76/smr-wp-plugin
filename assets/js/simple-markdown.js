
function markdownaParser(markdownText) {
    markdownText = markdownText.replace(/^#{1}\s+?(.+?)$/gm, "<h1>$1</h1>"); // h1
    markdownText = markdownText.replace(/^#{2}\s+?(.+?)$/gm, "<h2>$1</h2>"); // h2
    markdownText = markdownText.replace(/^#{3}\s+?(.+?)$/gm, "<h3>$1</h3>"); // h3
    markdownText = markdownText.replace(/^#{4}\s+?(.+?)$/gm, "<h4>$1</h4>"); // h4
    markdownText = markdownText.replace(/^#{5}\s+?(.+?)$/gm, "<h5>$1</h5>"); // h5
    markdownText = markdownText.replace(/^#{6}\s+?(.+?)$/gm, "<h6>$1</h6>"); // h5
    markdownText = markdownText.replace(/\[(.*?)\]\((.+?)\)/g, "<a href='$2' target='_blank'>$1</a>"); // link
    markdownText = markdownText.replace(/\*\*(.+?)\*\*/g, "<strong>$1</strong>"); // h5
    markdownText = markdownText.replace(/\*(.+?)\*/g, "<em>$1</em>"); // h5
    let lists = markdownText.match(/^(\s*([-\+\*]\s.+\n?)+)$/gm);
    
    lists?.forEach(list => {
        htmlList = list.replace(/^\s*[-\+\*]\s(.+\n?)$/gm, "<li>$1</li>");
        markdownText = markdownText.replace(list, "<ul>" + htmlList + "</ul>");
    });

    markdownText = markdownText.replace(/(?<=[\w\s\.])\n/g, "<br>"); // line break
    markdownText = markdownText.replace(/\n/g, ""); // line break
    
    return markdownText.trim();
}

function update(e) {
    let html = markdownaParser(e.target.value);
    e.target.previewElement.innerHTML = html;
}

// when document gets ready.
document.addEventListener('DOMContentLoaded', () => {
    let markdowns = [...document.querySelectorAll("[markdown]")];
    markdowns.forEach( markdown => {
        let preview = document.createElement("div");
        
        preview.classList.add("preview");
        preview.innerHTML = markdownaParser(markdown.value);

        markdown.after(preview);
        markdown.previewElement = preview;

        markdown.onkeyup = update;
    });
});