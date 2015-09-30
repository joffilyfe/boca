<?php
  
  $entrada = fgets(STDIN);

  while($entrada  !== 0) {
    printf("%d\n", $entrada+5);
    $entrada = (int)fgets(STDIN);
  }
?>