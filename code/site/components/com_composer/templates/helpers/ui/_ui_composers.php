<?php if ( count($composers) ) : ?>

<script src="com_composer/js/composer.js" />

<div id="com-composer-container">
    <ul class="nav nav-tabs" data-behavior="ComposerTabs">
    <?php $i=0; ?>
	<?php foreach($composers as $composer) : ?>
        <li <?= ($i == 0) ? 'class="active"' : '' ?>><a href="#"><?= $composer->title ?></a></li>
  		<?php $i++; ?>
    <?php endforeach;?>
    </ul>
    
    <div class="tab-content">
    <?php $i = 0?>
    <?php foreach($composers as $composer) : ?>
        <div data-behavior="PlaceHolder"  data-placeholder-element=".form-placeholder" data-placeholder-area="!#com-composer-container" data-trigger="LoadComposerTab" data-loadcomposertab-index="<?=$i++?>" data-content-url="<?=@route($composer->url)?>">
            <div class="form-placeholder"><span><?= $composer->placeholder ?></span></div>
        </div>
    <?php endforeach;?>
    </div>
</div>
<?php endif;?>