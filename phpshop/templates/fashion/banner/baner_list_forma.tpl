          <!-- Banner -->
          <a class="font-size-2 card bg-img-hero text-left h-100" href="@banerLink@" style="background-image: url('@banerImage@'); min-height: 250px;">
            <div class="card-body">
              <div class="mb-5">
                <h3 class="h2 banner-title" style="-webkit-filter: invert(@banerColor@%);filter: invert(@banerColor@%);" >@banerTitle@</h3>
                <span class="text-white small">@banerContent@</span>
              </div>
              <div class="card-footer border-0 bg-transparent pt-0">
              <span class="font-size-1 text-white font-weight-bold @php __hide('banerDescription'); php@ banner-title">@banerDescription@ <i class="fas fa-angle-right fa-sm ml-1"></i></span>                        
              </div>
            </div>
          </a>
          <!-- End Banner -->