<!-- pages -->

<?php 
$pageData = getActivePages();
?>
@if(!__isEmpty(getActivePages()))
<div class="dropdown lw-page-dropdown lw-dropdown ">
    <button type="button" class="btn nav-link dropdown-toggle" id="dropdownMenuButton" data-bs-toggle="dropdown" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
        {{ __tr('Pages') }}
    </button>
        <ul class="dropdown-menu dropdown-menu-right dropdown-menu-end shadow " aria-labelledby="dropdownMenuButton">
            @foreach($pageData as $pageKey => $pageValue)
            <li><a class="dropdown-item " href="{{ route('page.preview', [
                'pageUId' => $pageValue['_uid'],
                'slug' => slugIt($pageValue['slug']),
                ])}}"> {{  __tr($pageValue['title']) }}</a></li>
          @endforeach
        </ul>
</div>
  @endif
   <!-- /pages -->