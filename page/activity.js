var currentValue = 0;
function handleClick(myRadio) {
    currentValue = myRadio.value;
    if(currentValue == 1){
      document.getElementById("myInput").placeholder = "Szukaj po identyfikatorze";
      document.getElementById("myDate").type = "hidden";
    }
    else if(currentValue == 2){
      document.getElementById("myInput").placeholder = "Szukaj po IP";
      document.getElementById("myDate").type = "hidden";
    }
    else if(currentValue == 3){
      document.getElementById("myInput").placeholder = "Szukaj po URI";
      document.getElementById("myDate").type = "hidden";
    }
    else if(currentValue == 4){
      document.getElementById("myInput").placeholder = "Data początkowa";
      document.getElementById("myDate").placeholder = "Data końcowa";
      document.getElementById("myDate").type = "text";
    }
}


function searchEng() {
  var input, filter, table, tr, td, i, txtValue;
  input = document.getElementById("myInput");
  filter = input.value.toUpperCase();
  table = document.getElementById("activity-list");
  tr = table.getElementsByTagName("tr");
  for (i = 0; i < tr.length; i++) {
    td = tr[i].getElementsByTagName("td")[currentValue - 1];
    if (td) {
      txtValue = td.textContent || td.innerText;
      if (txtValue.toUpperCase().indexOf(filter) > -1) {
        tr[i].style.display = "";
      } else {
        tr[i].style.display = "none";
      }
    }       
  }
}


