<section class="same-section">
    <div class="row">
    <?php 
    // echo "<pre>"; 
    // print_r($giftcard->pageable);
    if(isset($giftcard->content[0]->productId))
    {
    foreach($giftcard->content as $gift) { ?>
    <div class="col-lg-3">
          <div class="main-box">
              <a href="javascript:void(0)" onclick="productDetail('{{base64_encode(base64_encode($gift->productId))}}')">
                  <div class="main-box-img">
                      <img src="{{$gift->logoUrls[0]}}" alt="image">
                  </div>
                  <div class="main-box-content">
                      <h2> {{ucwords($gift->productName)}} </h2>
                  </div>
              </a>
          </div>
      </div>
      <?php } ?>
      <?php }else{ 
      echo "Gift Card Not Found For The Selected Country";
       } ?>
      </div>

      <?php 
    // echo "<pre>"; 
    // print_r($giftcard->pageable);
    if(isset($giftcard->content[0]->productId))
    {  ?>
      <ul class="pagination">
      <?php 
      $rowsperpage = $giftcard->size;
      $totalpages  = $giftcard->totalPages;
      $currentpage = $giftcard->number+1;
      $offset = $giftcard->pageable->offset;
      $range = $totalpages;
      if ($currentpage > 1) {
        // show << link to go back to page 1
        echo " <li class='page-item'><a href='javascript:void(0)' onclick='getPlan(1)'><<</a></li>";
        // get previous page num
        $prevpage = $currentpage - 1;
        // show < link to go back to 1 page
        echo "<li class='page-item'><a href='javascript:void(0)' onclick='getPlan($prevpage)'  ><</a></li>";
     }

     // loop to show links to range of pages around current page
for ($x = ($currentpage - $range); $x < (($currentpage + $range) + 1); $x++) {
    // if it's a valid page number...
    if (($x > 0) && ($x <= $totalpages)) {
       // if we're on current page...
       if ($x == $currentpage) {
          // 'highlight' it but don't make a link
          echo "<li class='page-item'><a href='javascript:void(0)' class='active'>$x</a></li>";
       // if not current page...
       } else {
          // make it a link
          echo "<li class='page-item'><a href='javascript:void(0)' onclick='getPlan($x)'>$x</a> </li>";
       } // end else
    } // end if 
 } // end for
                  
 // if not on last page, show forward and last page links        
 if ($currentpage != $totalpages) {
    // get next page
    $nextpage = $currentpage + 1;
     // echo forward link for next page 
    echo "<li class='page-item'><a href='javascript:void(0)' onclick='getPlan($nextpage)'>></a> </li>";
    // echo forward link for lastpage
    echo "<li class='page-item'><a href='javascript:void(0)' onclick='getPlan($totalpages)'>>></a></li>";
 } // end if
      ?>
      </ul>
      <?php } ?>
      </section>
      <style>
      .main-box-content {
            background: #eee;
            text-align: center;
            padding: 15px 5px;
        }
        .main-box-content h2 {
            display: inline-block;
            font-size: 18px;
            color: #000;
            margin:0;
            line-height:1.4;
            font-weight: 500;
        }
        .main-box:hover{transform:translateY(10px)}
        .main-box {
            border: 1px solid rgb(0 0 0 / 8%);
            overflow: hidden;
            transition:0.4s; -webkit-transition:0.4s;
            border-radius: 10px;
        }
        .main-box-img img {width: 100%;}
        .same-section{padding:50px 0;}
        .same-section .row .col-lg-3{margin-bottom:30px;}
  </style>

<style>
.pagination a {
  color: black;
  float: left;
  padding: 8px 16px;
  text-decoration: none;
  transition: background-color .3s;
  border: 1px solid #ddd;
}

.pagination a.active {
  background-color: #4CAF50;
  color: white;
  border: 1px solid #4CAF50;
}

.pagination a:hover:not(.active) {background-color: #ddd;}
</style>