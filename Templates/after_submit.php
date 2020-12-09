<style>
    body.unavailable {
        pointer-events:none;
        opacity:0.5;
    }
</style>
<script>
    document.querySelector('form').addEventListener("submit", function() {
        document.querySelector('body').classList.add("unavailable");
    });
</script>