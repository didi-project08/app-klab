<div class="container-fluid-disable">
    <div class="page-header">
      <div class="row">
        <div class="col-lg-12">
          {{ $breadcrumb_title ?? '' }}
          <ol class="breadcrumb d-none d-md-block">
            <li class="breadcrumb-item"><a href="{{ $pathHome ?? '' }}">Home</a></li>
              {{ $slot ?? ''}}
          </ol>
        </div>
      </div>
    </div>
</div>