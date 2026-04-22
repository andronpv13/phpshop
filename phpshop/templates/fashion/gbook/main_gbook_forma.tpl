<!-- Review -->
  <div class="media">

    <div class="media-body border-bottom pb-6 mb-6">
      <a class="h4" href="/gbook/ID_@gbookId@.html" >@gbookName@</a>
      <p>@gbookOtsiv@</p>
      <small class="text-body mr-2">@gbookData@</small>

      <!-- Reply -->
      <div class="card bg-light shadow-none p-3 mt-4 @php __hide('gbookOtvet'); php@">
        <div class="media">
          <div class="avatar mr-3">
            <img class="avatar-img" src="@icon_dialog@" alt="">
          </div>
          <div class="media-body">
            <span class="d-block text-dark font-weight-bold">@name@</span>
            @gbookOtvet@
          </div>
        </div>
      </div>
      <!-- End Reply -->
    </div>
  </div>
  <!-- End Review -->