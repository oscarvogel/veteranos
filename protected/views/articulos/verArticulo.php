<div class="span12">
    <div class="span8">
        <div class="view" <?php echo (Yii::app()->params['EsMovil']) ? 'data-role="collapsible"' : '' ;?>>
            <h2>
                <?php echo CHtml::encode($model->Titulo); ?>
            </h2>
        
            <?php echo CHtml::decode($model->Texto); ?>
            <br />
        
            <!-- AddThis Button BEGIN -->
            <div class="addthis_toolbox addthis_default_style addthis_32x32_style">
            <a class="addthis_button_preferred_1"></a>
            <a class="addthis_button_preferred_2"></a>
            <a class="addthis_button_preferred_3"></a>
            <a class="addthis_button_preferred_4"></a>
            <a class="addthis_button_compact"></a>
            <a class="addthis_counter addthis_bubble_style"></a>
            </div>
            <script type="text/javascript">var addthis_config = {"data_track_addressbar":true};</script>
            <script type="text/javascript" src="//s7.addthis.com/js/300/addthis_widget.js#pubid=ra-4e9821cb04cae3bb"></script>
            <!-- AddThis Button END -->
        </div>
	</div>
    <div class="span4">
    	<script type="text/javascript"><!--
			google_ad_client = "ca-pub-1631748295721219";
			/* Articulos */
			google_ad_slot = "7418660737";
			google_ad_width = 250;
			google_ad_height = 250;
			//-->
			</script>
			<script type="text/javascript"
			src="http://pagead2.googlesyndication.com/pagead/show_ads.js">
		</script>
    </div>
</div>        
