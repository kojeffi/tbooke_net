	<footer class="footer">
				<div class="container-fluid">
					<div class="row text-muted">
						<div class="col-6 text-start">
							<p class="mb-0">
								<a class="text-muted" href="#" target="_blank"><strong>Tbooke</strong></a> - <a class="text-muted" href="#" target="_blank"><strong>Copyright 2024</strong></a>								&copy;
							</p>
						</div>
						<div class="col-6 text-end">
							<ul class="list-inline">
								<li class="list-inline-item">
									<a class="text-muted" href="#" target="_blank">Support</a>
								</li>
								<li class="list-inline-item">
									<a class="text-muted" href="#" target="_blank">Help Center</a>
								</li>
								<li class="list-inline-item">
									<a class="text-muted" href="#" target="_blank">Privacy</a>
								</li>
								<li class="list-inline-item">
									<a class="text-muted" href="#" target="_blank">Terms</a>
								</li>
							</ul>
						</div>
					</div>
				</div>
	</footer>
</div>

<div id="preloader" style="display: none;">
    <div class="loader"></div>
</div>
 <script>
	const notificationsClear = "{{ route('notifications.markAsRead') }}";
	const messagenotificationsClear = "{{ route('notifications.messagesmarkAsRead') }}";
	
 </script>
	<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11" type="text/javascript"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
 	<script src="https://cdnjs.cloudflare.com/ajax/libs/lightbox2/2.11.3/js/lightbox.min.js"></script>
	<script src="/docsupport/jquery-3.2.1.min.js" type="text/javascript"></script>
	<script src="/docsupport/chosen.jquery.js" type="text/javascript"></script>
	<script src="/docsupport/prism.js" type="text/javascript" charset="utf-8"></script>
	<script src="/docsupport/init.js" type="text/javascript" charset="utf-8"></script>
	<script src="https://cdn.tiny.cloud/1/omzlvo1v34uqcwchvwg1su29904hdb86emi5sr5agotnloym/tinymce/7/tinymce.min.js" referrerpolicy="origin"></script>
	<script src="/js/style.js"></script>
	<script src="/js/custom.js"></script>
	<script src="/static/js/app.js"></script>
		<script>
function fill(Value) {
 //Assigning value to "search" div in "search.php" file.
  $('#search').val(Value);
 //Hiding "display" div in "search.php" file.
  $('#display').hide();
}
$(document).ready(function() {
 //On pressing a key on "Search box" in "search.php" file. This function will be called.
  $("#search").keyup(function() {
     //Assigning search box value to javascript variable named as "name".
      var name = $('#search').val();
     //Validating, if "name" is empty.
      if (name == "") {
         //Assigning empty value to "display" div in "search.php" file.
          $("#display").html("");
      }
     //If name is not empty.
      else {
         //AJAX is called.
		 var searchPostUri = "{{ route('feed-search')}}";
		 var PostUri = "{{ url('feed-details')}}";
		 //console.log(PostUri)
          $.ajax({
             //AJAX type is "Post".
              type: "GET",
             //Data will be sent to "ajax.php".
              url: searchPostUri,
             //Data, that will be sent to "ajax.php".
              data: {
                 //Assigning value of "name" into "search" variable.
                  search: name
              },
             //If result found, this funtion will be called.
              success: function(html) {
				//console.log(html.data);
				let arr = html.data;
			
				var result = ""
				for (let index = 0; index < arr.length; index++) {
					const element = arr[index];
					//console.log(element)
					
					result = result + "<a class='list-group-item' href='"+PostUri+'/'+ element.id + "'>"+ element.content + "</a>";
				}
                 //Assigning result to "display" div in "search.php" file.
                  $("#display").html(result).show();
              }
          });
      }
  });
});
</script>
<style>
.list-group {
	margin-top:10px;
    background-color: white;
    /* display: none; */
    list-style-type: none;
    /* margin: 0 0 0 10px; */
    padding: 0;
    position: absolute;
	z-index: 9999;
    width: 80%;
}

.list-group > li {
    border-color: gray;
    border-image: none;
    border-style: solid solid none;
    border-width: 1px 1px 0;
    padding-left: 0px;
}

.list-group > li:last-child {
  border-bottom: 1px solid gray;
}

.form-control:focus + .list-group {
  display: block; 
}
</style>
</body>
</html>