<input type='hidden' id='my_id' value='<?php echo User::current ()->id;?>' />


<div class='panel'>
  <header>
    <h2>個人行事曆</h2>
<?php if (User::current ()->in_roles (array ('project'))) { ?>
        <a id='search' class='icon-s'></a>
<?php } ?>
  </header>

<?php if (User::current ()->in_roles (array ('project'))) { ?>
        <header class='customize' id='users'>
          <?php
          foreach (User::all () as $user) {
            if (User::current ()->id != $user->id) {?>
              <label class='checkbox'>
                <input type='checkbox' value='<?php echo $user->id;?>' <?php echo User::current ()->id == $user->id ? 'checked' : '';?> />
                <span></span>
                <?php echo $user->name;?>
              </label>
          <?php
            }
          } ?>
        </header>
<?php } ?>

  <div class='calendar'>
    <div class='daySchedule'></div>
    <div class='year_months'>
      <a class='icon-al'></a>
      <div class='title'></div>
      <a class='icon-ar'></a>
    </div>
    <div class='months'></div>
  </div>
</div>