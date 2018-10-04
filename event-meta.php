<?php
// retrieves the stored values from the database
$from_date = get_post_meta( get_the_ID(), 'eap_from_day', true );
$from_time = get_post_meta( get_the_ID(), 'eap_from_time', true );
$until_date = get_post_meta( get_the_ID(), 'eap_until_day', true );
$until_time = get_post_meta( get_the_ID(), 'eap_until_time', true );
$location = get_post_meta( get_the_ID(), 'eap_location', true );
$city = get_post_meta( get_the_ID(), 'eap_city', true );
$link_location = get_post_meta( get_the_ID(), 'eap_link_location', true );

// format 'from date'
$from_date = eap_format_date($from_date);
$from_date[1] = eap_make_month_translatable($from_date[1]);
// if 'until date' is set format 'until date'
if ($until_date) {
  $until_date = eap_format_date($until_date);
  $until_date[1] = eap_make_month_translatable($until_date[1]);
}
// to avoid notices
if (!$from_time) {
  $from_time = '';
} else {
  // add a comma in before if it's set
  $from_time = ', ' . $from_time;
}

if (!$until_time) {
  $until_time = '';
}

// separation mark '-' between from day/time and until day/time
$separation_mark = ' – ';
// comma between location and city and after year
$comma_dt = ', '; // dt = datetime
$comma_loc = ', ';

// logic for commas and separation mark...

// if until date AND until time are not set
if ( !$until_date && ($until_time == '') ) {
  $separation_mark = '';
  $comma_dt = '';
  // if until date is set AND until time is not
} elseif ( $until_date && ($until_time == '') ) {
  // if dates are NOT the same day
  if ($until_date != $from_date) {
    $comma_dt = '';
    // if dates ARE the same day
  } elseif ($until_date == $from_date) {
    for ( $i = 0; $i < sizeof( $until_date ); $i++ ) {
      $until_date[$i] = '';
    }
    $separation_mark = '';
    $comma_dt = '';
  }
  // until date AND until time are both set
} elseif ( $until_date && ($until_time != '') ) {
  // if same day
  if ($until_date == $from_date) {
    for ( $i = 0; $i < sizeof( $until_date ); $i++ ) {
      $until_date[$i] = '';
    }
    $comma_dt = '';
  }
  // until date is NOT set AND until time is set
} elseif ( !$until_date && ($until_time != '') ) {
  if ($until_time == $from_time) {
    $separation_mark = '';
    $until_time = '';
  }
  $comma_dt = '';
}
// if the city is not set it doesn't display the comma
if (!$city) {
  $comma_loc = '';
}
?>

<div class="eap__meta">
  <span class="eap__date"><?php echo $from_date[0] . ' ' . $from_date[1] . ' ' . $from_date[2] ?></span><span class="eap__time"><?php echo $from_time ?></span><?php echo $separation_mark ?>
  <span class="eap__date">
    <?php
    if ($until_date) {
      echo $until_date[0] . ' ' . $until_date[1] . ' ' . $until_date[2] . $comma_dt;
    }
    ?>
  </span>
  <span class="eap__time"><?php echo $until_time ?></span>
  <br>
  <?php if ($link_location) : ?>
    <a href="<?php echo $link_location ?>" target="_blank" class="eap__location"><?php echo $location ?></a>
  <?php else : ?>
    <span class="eap__location"><?php echo $location ?></span>
  <?php endif; ?>
  <?php echo $comma_loc ?>
  <span class="eap__city"><?php echo $city ?></span>
  <p class="eap__additional-info">
    <?php do_action( 'eap_additional_info', get_the_ID() ); ?>
  </p>
</div>
