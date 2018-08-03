@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">Product Import/Export</div>

                <div class="card-body">
                	@if(Session::has('error'))
               	    <div class="alert alert-danger">

				      </button>
				      <strong>Failed!</strong> {{ Session::get('error') }} 
				    </div>
				    @endif
				    @if(Session::has('errors'))
				    <div class="alert alert-danger">

				       <strong>Failed!</strong> 
					 	 <ul>
					      <?php foreach(Session::get('errors') as $row=>$err){ ?> 
					      	<li>
					      		row <?php echo $row+1; ?>
						      	<ul>
						      	<?php foreach($err as $er){ ?>
						      		<?php foreach($er as $e){ ?>
						      			<li>
						      			<?php echo $e; ?>
						      			</li>
						      		<?php } ?>
						      	<?php } ?>
						      	</ul>
					      	</li>
					      <?php } ?>
					      </ul>
				      </div>
				    @endif  
                    @if(Session::has('success'))
					<div class="alert alert-success">
				      </button>
				      <strong>Success!</strong> {{ Session::get('success')}}
				    </div>
					@endif
				<br />
				<a href="{{ URL::to('downloadExcel/xls') }}"><button >Download Excel xls</button></a>
				<a href="{{ URL::to('downloadExcel/xlsx') }}"><button c>Download Excel xlsx</button></a>
				<a href="{{ URL::to('downloadExcel/csv') }}"><button >Download CSV</button></a>
				<form  action="{{ URL::to('importExcel') }}" class="form-horizontal" method="post" enctype="multipart/form-data">
					{{ csrf_field() }}
					<input type="file" name="import_file" />
					<button >Import File</button>
				</form>
                    
                </div>
            </div>
        </div>
    </div>
</div>
@endsection