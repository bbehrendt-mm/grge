<h1 class="noclick"><i class="fa fa-arrow-circle-right"></i>Reflection Atlas</h1>

<div class="row">
    <div class="cell ro-1 rw-10 padded">
        <div class="note">
            <b>Welcome to the Reflection Atlas</b><br />
            This tool visualizes aspects of the game's balancing by performing meta-analysis on the game code.<br /><br />
            Since this analysis is quite resource intensive, please refrain from making unnecessary requests to this tool when working on a live server.<br /><br />
            Below, you'll find a list of preset data visualizations.
        </div>
    </div>

    <div class="cell ro-1 rw-10 padded">
        <a href="#" data-hrefto="items"><i class="fa fa-arrow-circle-right"></i> Item Database</a> <br>
        <a href="#" data-hrefto="locations"><i class="fa fa-arrow-circle-right"></i> Location Database</a>
    </div>
</div>

<script type="application/javascript">
    // ## JS COMPRESS BEGIN ## //
    $('[data-hrefto]').click(function() {
        game.network.load($(this).data('hrefto') ? ('admin/wiki/' + $(this).data('hrefto')) : 'admin/wiki');
    });
    // ## JS COMPRESS END ## //
</script>