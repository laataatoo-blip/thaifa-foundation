<!-- loaderPage -->
<link rel="stylesheet" href="./assets/css/loaderPage.css?">
<div id="PageLoader"></div>
<script>
    $("#PageLoader").fadeOut(0);
    $("#PageLoader").append(`
        <div class="background-spinner">
            <div class="spinner"></div>
        </div>
    `);
    $("a").click((e) => {
        e.preventDefault();
        e.stopPropagation();
        const href = $(e.currentTarget).attr('href')
        $("#PageLoader").fadeIn();
        setTimeout(() => {
            window.location.href = href;
        }, 500);
    })
</script>