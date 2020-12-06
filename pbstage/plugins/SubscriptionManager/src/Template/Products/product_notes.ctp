<style>
* {
  box-sizing: border-box;
}
body {
  font: 16px Arial;  
}
.autocomplete {
  /*the container must be positioned relative:*/
  position: relative;
  display: inline-block;
}
#autoCompleteDescription {
  border: 1px solid transparent;
  background-color: #f1f1f1;
  padding: 10px;
  font-size: 16px;
}
#autoCompleteDescription[type=text] {
  background-color: #f1f1f1;
  width: 100%;
}
.autocomplete-items {
  position: absolute;
  border: 1px solid #d4d4d4;
  border-bottom: none;
  border-top: none;
  z-index: 99;
  /*position the autocomplete items to be the same width as the container:*/
  top: 100%;
  left: 15px;
  right: 15px;
}
.autocomplete-items div {
  text-align:left;
  padding: 10px;
  cursor: pointer;
  background-color: #fff; 
  border-bottom: 1px solid #d4d4d4; 
}
.autocomplete-items div:hover {
  /*when hovering an item:*/
  background-color: #e9e9e9; 
}
.autocomplete-active {
  /*when navigating through the items using the arrow keys:*/
  background-color: DodgerBlue !important; 
  color: #ffffff; 
}
</style>

<?php echo $this->Element('Products/top_menu'); ?>
<section class="content col-sm-12 col-xs-12">
	<div class="col-sm-12 col-xs-12"><!-- start of right_part -->
        <div class="col-md-12 col-sm-12 col-xs-12 no-padding table_main_div tree_table"><!-- start of tab -->
			<?php echo $this->Element('Products/sub_menu'); ?>
            <div id="myTabContent" class="tab-content tab_div_content"><!-- start of right_part -->
				<div class="tab-pane fade in active col-sm-12 col-xs-12"><!-- start of content_1 -->
				<section class="content col-sm-12 col-xs-12">
					<div class="col-sm-12 col-xs-12 no-padding"><!-- start of pagination or buttons -->
						<div class="col-md-12 col-sm-12 col-xs-12 no-padding-right xs-no-padding buttons_div">
							<?=$this->Form->create($addnotes, ['enctype' => 'multipart/form-data', 'class' => 'form-horizontal', 'novalidate' => true]);?>
							<div class="box-body">
                                <div class="form-group col-sm-3 col-xs-3">
									<div class="col-sm-12">
										<div class="col-sm-4">Country:</div><div class="col-sm-8"><?=$this->Form->select('ProductNotes.location_id', $locations, ['class' => 'form-control'])?></div>
                                    </div>
                                    <div class="col-sm-12">
										<div class="col-sm-4">Note:</div><div class="col-sm-8"><?=$this->Form->select('ProductNotes.title', $this->Admin->productNote, ['class' => 'form-control'])?></div>
                                    </div>
                                </div>
                                <div class="form-group col-sm-6 col-xs-6">
                                    <label for="Description" class="col-sm-2 control-label">Keyword:</label>
                                    <div class="col-sm-10">
										<?=$this->Form->text('ProductNotes.description', ['id'=>'autoCompleteDescription', 'value'=>'', 'placeholder'=>'Search', 'aria-label'=>'Search', 'class' => 'form-control'])?>
                                    </div>
                                </div>
                                <div class="form-group col-sm-3 col-xs-3">
                                    <div class="col-sm-12">
									<?=$this->Form->select('ProductNotes.is_active', ['active' => 'Active', 'in_active' => 'In Active'], ['class' => 'form-control'])?><hr />
                                       <?=$this->Form->button('Save', ['type' => 'submit', 'class' => 'btn btn-div-buy btn-1b']);?>
                                    </div>
                                </div>
                            </div>
							<?=$this->Form->end();?>
						</div>
						<div class="col-md-12 col-sm-12 col-xs-12 no-padding-left xs-no-padding"><!-- start of pagination -->
							<?php echo $this->Element('pagination'); ?>
						</div><!-- end of pagination -->
					</div><!-- end of pagination or buttons -->
					<div class="col-sm-12 col-xs-12 table_view responsive-mobile-table"><!-- start of table -->
						<table class="col-xs-12 table-bordered table-hover table-condensed no-padding no-border">
							<thead>
								<tr><th>S No</th><th>Country</th><th>Title</th><th>Description</th><th class="text-center">Action</th></tr>
							</thead>
							<tbody>
                    <?php $i = 1;
foreach ($notes as $value): ?>
								<tr>
									<td data-title="S No"><?php echo $i++; ?></td>
									<td data-title="Country"><?=h($this->Admin->checkValue($value->location->title))?></td>
									<td data-title="Title"><?=h($this->Admin->checkValue($value->title))?></td>
									<td data-title="Description"><?=h($this->Admin->checkValue($value->description))?></td>
									<td data-title="Action" class="text-center">
										<?php echo $this->Form->postLink('<i class="fa fa-trash"></i>', ['action' => 'productNotes', $id, 'key', md5($id)], ['block' => false, 'data' => ['noteId' => $value->id], 'method' => 'delete', 'escape' => false, 'confirm' => __('Are you sure you want to delete # {0}?', $value->id)]); ?>
									</td>
								</tr>
					<?php endforeach;?>
							</tbody>
						</table>
					</div><!-- end of table -->
				</section>
                </div><!-- end of right_part -->
            </div><!-- end of profile -->

        </div><!-- end of right_part -->

    </div><!-- end of tab -->
</section>

<script>
function autocomplete(inp, arr) {
  /*the autocomplete function takes two arguments,
  the text field element and an array of possible autocompleted values:*/
  var currentFocus;
  /*execute a function when someone writes in the text field:*/
  inp.addEventListener("input", function(e) {
      var a, b, i, val = this.value;
      /*close any already open lists of autocompleted values*/
      closeAllLists();
      if (!val) { return false;}
      currentFocus = -1;
      /*create a DIV element that will contain the items (values):*/
      a = document.createElement("DIV");
      a.setAttribute("id", this.id + "autocomplete-list");
      a.setAttribute("class", "autocomplete-items");
      /*append the DIV element as a child of the autocomplete container:*/
      this.parentNode.appendChild(a);
      /*for each item in the array...*/
      for (i = 0; i < arr.length; i++) {
        /*check if the item starts with the same letters as the text field value:*/
        if (arr[i].substr(0, val.length).toUpperCase() == val.toUpperCase()) {
          /*create a DIV element for each matching element:*/
          b = document.createElement("DIV");
          /*make the matching letters bold:*/
          b.innerHTML = "<strong>" + arr[i].substr(0, val.length) + "</strong>";
          b.innerHTML += arr[i].substr(val.length);
          /*insert a input field that will hold the current array item's value:*/
          b.innerHTML += "<input type='hidden' value='" + arr[i] + "'>";
          /*execute a function when someone clicks on the item value (DIV element):*/
          b.addEventListener("click", function(e) {
              /*insert the value for the autocomplete text field:*/
              inp.value = this.getElementsByTagName("input")[0].value;
              /*close the list of autocompleted values,
              (or any other open lists of autocompleted values:*/
              closeAllLists();
          });
          a.appendChild(b);
        }
      }
  });
  /*execute a function presses a key on the keyboard:*/
  inp.addEventListener("keydown", function(e) {
      var x = document.getElementById(this.id + "autocomplete-list");
      if (x) x = x.getElementsByTagName("div");
      if (e.keyCode == 40) {
        /*If the arrow DOWN key is pressed,
        increase the currentFocus variable:*/
        currentFocus++;
        /*and and make the current item more visible:*/
        addActive(x);
      } else if (e.keyCode == 38) { //up
        /*If the arrow UP key is pressed,
        decrease the currentFocus variable:*/
        currentFocus--;
        /*and and make the current item more visible:*/
        addActive(x);
      } else if (e.keyCode == 13) {
        /*If the ENTER key is pressed, prevent the form from being submitted,*/
        e.preventDefault();
        if (currentFocus > -1) {
          /*and simulate a click on the "active" item:*/
          if (x) x[currentFocus].click();
        }
      }
  });
  function addActive(x) {
    /*a function to classify an item as "active":*/
    if (!x) return false;
    /*start by removing the "active" class on all items:*/
    removeActive(x);
    if (currentFocus >= x.length) currentFocus = 0;
    if (currentFocus < 0) currentFocus = (x.length - 1);
    /*add class "autocomplete-active":*/
    x[currentFocus].classList.add("autocomplete-active");
  }
  function removeActive(x) {
    /*a function to remove the "active" class from all autocomplete items:*/
    for (var i = 0; i < x.length; i++) {
      x[i].classList.remove("autocomplete-active");
    }
  }
  function closeAllLists(elmnt) {
    /*close all autocomplete lists in the document,
    except the one passed as an argument:*/
    var x = document.getElementsByClassName("autocomplete-items");
    for (var i = 0; i < x.length; i++) {
      if (elmnt != x[i] && elmnt != inp) {
        x[i].parentNode.removeChild(x[i]);
      }
    }
  }
  /*execute a function when someone clicks in the document:*/
  document.addEventListener("click", function (e) {
      closeAllLists(e.target);
      });
}

/*An array containing all the country names in the world:*/
var algoNotesJS = <?php echo json_encode($algoNotes); ?>;
/*initiate the autocomplete function on the "myInput" element, and pass along the countries array as possible autocomplete values:*/
autocomplete(document.getElementById("autoCompleteDescription"), algoNotesJS);
</script>
