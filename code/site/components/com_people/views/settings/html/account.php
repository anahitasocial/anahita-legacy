<?php defined('KOOWA') or die; ?>

<h3><?= @text('COM-PEOPLE-PROFILE-EDIT-ACCOUNT-INFORMATION') ?></h3>

<?php
    $user = JFactory::getUser($entity->userId);
?>

<div>
    <div class="user">
            
        <form data-behavior="FormValidator" action="<?php print JRoute::_( 'index.php' ); ?>" method="post" name="userform" id="userform" autocomplete="off">

         <div class="control-group">
            <label class="control-label"  for="username">
                <?php print JText::_( 'User name' ); ?>:
            </label>
            <div class="controls">
                <input data-validators="required validate-remote url:'<?=@route('view=person')?>'" type="text" id="username" name="username" value="<?php print @escape($user->get( 'username' ));?>" maxlength="25" />
            </div>
        </div>
        
        <div class="control-group">
            <label class="control-label"  for="email">
                <?php print JText::_( 'Email' ); ?>:
            </label>
            <div class="controls">
                <input data-validators="required validate-email validate-remote url:'<?=@route('view=person')?>'" type="text" id="email" name="email" value="<?php print @escape($user->get( 'email' ));?>" maxlength="100" />
            </div>
        </div>
        
        <div class="control-group">
            <label class="control-label"  for="password">
                <?php print JText::_( 'Password' ); ?>:
            </label>
            <div class="controls">
                <input type="password" id="password" name="password" value="" />
            </div>
        </div>
        
        <div class="control-group">
            <label class="control-label"  for="password2">
                <?php print JText::_( 'Verify Password' ); ?>:
            </label>
            <div class="controls">
                <input data-validators="validate-match matchInput:'password' matchName:'<?php print JText::_( 'Password' )?>'" type="password" id="password2" name="password2" value="" />
            </div>
        </div>
        <?php        
            $user   = JFactory::getUser($entity->userId);
            $params = $user->getParameters(true)->renderToArray();        
        ?>                
        <div class="control-group">
            <label class="control-label" for="timezone">
                <?php print JText::_( 'Time Zone' ); ?>:
            </label>
            <div class="controls">
                <?php print $params['timezone'][1] ?>
            </div>
        </div>
        
        <div class="form-actions">
            <button type="submit" class="btn" onclick="submitbutton( this.form );return false;"><?php print JText::_('Save'); ?></button>
        </div>
            
        <input type="hidden" name="username" value="<?php print $user->get('username');?>" />
        <input type="hidden" name="id" value="<?php print $user->get('id');?>" />
        <input type="hidden" name="gid" value="<?php print $user->get('gid');?>" />
        <input type="hidden" name="option" value="com_user" />
        <input type="hidden" name="task" value="save" />
        <?php print JHTML::_( 'form.token' ); ?>
        </form>

    </div>
</div>