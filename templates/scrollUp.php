<style>
    /* Scroll to Top button */
    #scrollToTopBtn {
        display: none;
        position: fixed;
        bottom: 80px;
        right: 30px;
        z-index: 99;
        font-size: 18px;
        border: none;
        outline: none;
        background-color: #555;
        color: white;
        cursor: pointer;
        padding: 15px;
        border-radius: 4px;
    }
    
    #scrollToTopBtn:hover {
        background-color: black;
    }
</style>

<!--Scroll to Top button-->
<button onclick="topFunction()" id="scrollToTopBtn" title="Go to top">Top</button>

<script>
    // When the user scrolls down 20px from the top of the document, show the button
    window.onscroll = function() {scrollFunction()};
    
    function scrollFunction() {
        if (document.body.scrollTop > 20 || document.documentElement.scrollTop > 20) {
            document.getElementById("scrollToTopBtn").style.display = "block";
        } else {
            document.getElementById("scrollToTopBtn").style.display = "none";
        }
    }
    
    // When the user clicks on the button, scroll to the top of the document
    function topFunction() {
        document.body.scrollTop = 0;
        document.documentElement.scrollTop = 0;
    }
    
</script>