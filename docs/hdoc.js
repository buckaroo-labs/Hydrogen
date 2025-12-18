
$(document).ready(function(){



    /******************************************** 
     * THIS SECTION: TABLE-OF-CONTENTS GENERATION
    *********************************************/

    //The document title is considered level 1 and is shown in an H1 heading
    heading1=document.createElement( "h1");
    heading1.classList.add("HDocH1");
    heading1.innerHTML= document.title;

    TOCDiv = document.createElement("div");
    TOCDiv.id = "HDocTOC";
    TOCH2 = document.createElement("h2");
    TOCH2.innerHTML="Contents";
    TOCH2.classList.add("HDocH2");
    TOCDiv.appendChild(TOCH2);

    TOC = document.createElement("ul");
    TOC.id = "HDocTOCul";

    //Top-level contents are considered level 2 and are given H2 headings
    $(".contentsL2").each(function( index ) {
        //console.log( index + ": " + $( this ).text() );
        heading2 = document.createElement( "h2");
        heading2.classList.add("HDocH2");
        //Do not show ID string suffix beginning with double-underscore
        temp=$(this).attr("id").split("__")[0];
        //replace single underscore with space for heading text
        hText = temp.replace(/_/g,' ');
        heading2.innerHTML= hText;
        $(this).prepend(heading2);
        TOCElement=document.createElement("li");
        TOCElement.classList.add("HDocTOCli");
        TOCElement.classList.add("HDocTOCli1");
        TOCElement.innerHTML='<a href="#' + $(this).attr("id") + '">' + hText + '</a>'
        TOC.append(TOCElement);

        

        //append the level-3 list items to the TOC
        $(this).children(".contentsL3").each(function ( index) {
            TOCElement=document.createElement("li");
            TOCElement.classList.add("HDocTOCli");
            TOCElement.classList.add("HDocTOCli2");
            //Do not show ID string suffix beginning with double-underscore
            temp=$(this).attr("id").split("__")[0];
            //replace single underscore with space for heading text
            hText = temp.replace(/_/g,' ');

            TOCElement.innerHTML='<a href="#' + $(this).attr("id") + '">' + hText + '</a>'
            TOC.append(TOCElement);

            //create the level-3 headings
            heading3= document.createElement( "h3");
            heading3.classList.add("HDocH3");
            heading3.innerHTML= hText;
            $(this).prepend(heading3);
        });
    });

    TOCDiv.appendChild(TOC);
    $("body").prepend(TOCDiv);
    $("body").prepend(heading1);

    /******************************************** 
     * THIS SECTION: FOOTER GENERATION
    *********************************************/

    //Credit where credit is due
    footer=document.createElement("footer");
    footer.id="HDocFooter";
    footer.innerHTML='Powered by <a href="metadoc.html">HDoc</a>';
    $("body").append(footer);

    /******************************************** 
     * THIS SECTION: TOP-OF-DOCUMENT BUTTON GENERATION
    *********************************************/
    btn=document.createElement("button");
    btn.id="topBtn";
    btn.innerHTML="top";
    btn.title="Go to top";
    $("body").prepend(btn);

    // When the user scrolls down 20px from the top of the document, show the button
    window.onscroll = function() {scrollFunction()};
    let topButton = document.getElementById("topBtn");
    function scrollFunction() {
    if (document.body.scrollTop > 20 || document.documentElement.scrollTop > 20) {
        topButton.style.display = "block";
    } else {
        topButton.style.display = "none";
    }
    }

    $("#topBtn").click(function () {
        document.body.scrollTop = 0;
        document.documentElement.scrollTop = 0;
    });


   
    /******************************************** 
     * THIS SECTION: CITATIONS and INLINE-NOTES
    *********************************************/
    var citations = document.querySelectorAll("q,blockquote,span");
    var endNotes = document.createElement("div");
    endNotes.classList.add("HDocEndNotes");
    var endH2=document.createElement("H2");
    endH2.innerHTML="End Notes";
    endH2.id="HDocEnd";
    endNotes.appendChild(endH2);

    var j=1;
    for (var i=0; i<citations.length; i++) {
        if (citations[i].hasAttribute("cite")) {
            if (citations[i].id=="") {citations[i].id="_citation_" + j}
            if (citations[i].tagName=="Q") {
                $('<a class="citation" target="_blank" href="' + citations[i].cite + '"><sup>' + j + '</sup></a>&nbsp; ').insertAfter("#" + citations[i].id);
                var newNote=document.createElement("p");
                newNote.innerHTML=j + ". " + citations[i].cite ;
                endNotes.appendChild(newNote);
            } else {
                var citationLink = document.createElement("a");
                citationLink.href= citations[i].attributes["cite"].value ;
                citationLink.target="_blank";
                var superscr = document.createElement("sup");
                superscr.innerHTML=j;
                citationLink.appendChild(superscr);
                citations[i].appendChild(citationLink);
                var newNote=document.createElement("p");
                newNote.classList.add("HDocEndNoteItem")
                newNote.innerHTML=j + ". " + citations[i].attributes["cite"].value ;
                endNotes.appendChild(newNote);
            }
            j++;
        }
    }
    if (citations.length>0) {
        $("body").append(endNotes);
    }

    var inlineNotes =  document.querySelectorAll("inline-note");

    for (var i=0; i<inlineNotes.length; i++) {
        inlineNotes[i].innerHTML ="";
        if (inlineNotes[i].hasAttribute("id")===false) {
            inlineNotes[i].id="_inline_note_" + i;
        }
        //var inlineNoteID = inlineNotes[i].id;
        var asterisk=document.createElement("span");
        asterisk.innerHTML="**";
        asterisk.style.fontStyle="bold";
        asterisk.style.color="#FF0000"
        asterisk.id = "_inline_note_inner_span_" + i;
        inlineNotes[i].appendChild(asterisk);
        inlineNotes[i].addEventListener("click", function() {this.innerHTML= " " + this.title;});
    }

});