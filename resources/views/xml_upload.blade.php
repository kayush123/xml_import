<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<meta name="csrf-token" content="{{ csrf_token() }}" data-url="{{URL('')}}">
	<title>Xml Import</title>
	<link rel="stylesheet" type="text/css" href="{{ asset('asset/css/bootstrap.min.css') }}">
	<link rel="stylesheet" type="text/css" href="{{ asset('asset/css/messi.min.css')}}">
	<link rel="stylesheet" type="text/css" href="{{ asset('style.css') }}">
</head>
<body>
	<!-- session meesage -->
	@if(Session::has('success'))
		<p class="alert alert-success message">{{ Session::get('success') }}</p>
	@endif
	@if(Session::has('error'))
		<p class="alert alert-danger message">{{ Session::get('error') }}</p>
	@endif
	<div class="container">
		<h3>Upload Xml File</h3>
		<form action="{{route('upload.xml')}}" method="post" class="upload_form" enctype="multipart/form-data">
			@csrf
			<div class="input-group">
			  	<input type="file" class="form-control" name="xml_file" id="input_file" aria-describedby="inputGroupFileAddon04" aria-label="Upload">
			  	<button class="btn btn-outline-secondary submit_btn" type="submit" id="inputGroupFileAddon04">Upload</button>
			</div>
			<p class="hidden error text text-danger">*Please Select file to upload!</p>
		</form>

	</div>
	<div class="listing">
		@if($files->count() > 0)
		<table class="table table-striped listing-table">
			<tr>
				<th style="width:15%">S.No.</th>
				<th style="width:25%">File</th>
				<th style="width:25%">Uploaded At</th>
				<th style="width:35%">Action</th>
			</tr>
			@php $count = 1; @endphp
			@foreach($files as $item)
			<tr>
				<td>{{$count}}</td>
				<td>{{$item->file_name ?? ""}}</td>
				<td>{{date('Y-m-d h:i a')}}</td>
				<td data-item-id="{{$item->id}}">
					@if($item->status == 0)
					<a href="javascript:void(0)" data-url="{{route('import.xml')}}" class="btn btn-info btn-sm import">Import</a>
					@endif
					<a href="javascript:void(0)" data-url="{{route('xml.destroy')}}"  class="btn btn-danger btn-sm delete">Delete</a>

				</td>
			</tr>
			@php $count++; @endphp
			@endforeach
		</table>
		@endif
	</div>
	<script type="text/javascript" src="{{ asset('asset/js/jquery.js') }}"></script>
	<script type="text/javascript" src="{{ asset('asset/js/bootstrap.min.js') }}"></script>
	<script type="text/javascript" src="{{ asset('asset/js/messi.min.js')}}"></script>
	<script>
		$(document).ready(function(){
			function showMessage(message){
				new Messi(message, {
					center:false,
					autoclose:5000,
					viewport: {top: '10px', left: '450px'},
				});
			}
			
			setTimeout(function(){
				$('.message').fadeOut();
			}, 2000);

			// upload form validation
			$('.submit_btn').on('click', function(e){
				e.preventDefault();
				let form = $('.upload_form');
				let input = $('#input_file').val();
				let error = $('.error');
				if(input != ""){
					form.submit();
				} else {
					error.removeClass('hidden');
				}

			});
			// import xml file via ajax
			$('.import').on('click', function(){
				var url = $(this).data('url');
				var item_id = $(this).parents('td').data('item-id');
				$.ajax({
					url: url,
					headers: {
		                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
		            },
		            type: "POST",
		            data: {item_id:item_id},
		            cache: false,
		            success:function(response){
		            	if(response.status == true){
		            		showMessage('File Imported Successfully');
		            		// new Messi('File Imported Successfully', {
							// 	autoclose:2000,
							// 	viewport: {top: '10px', left: '10px'},
							// });
		            	} else {
		            		showMessage('Some Error Occured, Please Try After Sometime!');
		            		// new Messi('Some Error Occured, Please Try After Sometime!', {
							// 	autoclose:2000,
							// 	viewport: {top: '10px', left: '10px'},
							// });
		            	}
		            },
		            error:function(error){
		            	console.log(error);
		            	showMessage('Some Error Occured, Please Try After Sometime!');
		            	// new Messi('Some Error Occured, Please Try After Sometime!', {	
						// 	autoclose:2000,
						// 	viewport: {top: '10px', left: '10px'},
						// });
		            }
		        });
		    });

		    $('.delete').on('click', function(){
		    	let url = $(this).data('url');
		    	let item_id = $(this).parents('td').data('item-id');
		    	var current = $(this);
		    	let query = new Messi.ask('Are you sure to delete this file?', function(value){
		    		if(value == 'Y'){
		    			$.ajax({
		    				url: url,
							headers: {
				                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
				            },
				            type: "POST",
				            data: {item_id:item_id},
				            cache: false,
				            success:function(response){
				            	if(response.status == true){
									current.parents('tr').remove();
									let table_length = $('.listing-table').find('tr').length;
									if(table_length <= 1){
										$('.listing-table').remove();
									}
									showMessage('File Deleted Successfully');
				            		// new Messi('File Deleted Successfully', {
									// 	autoclose:5000,
									// 	viewport: {top: '10px', left: '10px'},
									// });
				            	} else {
				            		showMessage('Some Error Occured, Please Try After Sometime!');
				            		// new Messi('Some Error Occured, Please Try After Sometime!', {
									// 	autoclose:5000,
									// 	viewport: {top: '10px', left: '10px'},
									// });
				            	}
				            },
				            error:function(error){
				            	console.log(error);
				            	showMessage('Some Error Occured, Please Try After Sometime!');
				            	// new Messi('Some Error Occured, Please Try After Sometime!', {	
								// 	autoclose:2000,
								// 	viewport: {top: '10px', left: '10px'},
								// });
				            },
		    			})
		    		}
		    	},{
		    		zIndex:9999,
		    		center:false,
					viewport: {top: '10px', left: '400px'},
		    	})
		    })
		});
	</script>
</body>
</html>