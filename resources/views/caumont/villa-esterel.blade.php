<header class="site">
  <div class="wrap site-inner">
    <a href="{{ route('caumont') }}" class="brand" aria-label="Caumont">
      <span class="monogram"><img src="{{ asset('assets/caumont/monogram.svg') }}" alt=""></span>
      <span>Caumont</span>
    </a>
    <nav class="primary" aria-label="Primary">
      <a href="{{ route('caumont') }}#portfolio">Portfolio</a>
      <a href="{{ route('caumont') }}#management">Management</a>
      <a href="{{ route('caumont') }}#realestate">Real Estate</a>
      <a href="{{ route('caumont') }}#contact">Contact</a>
    </nav>
    <a href="{{ route('caumont') }}#portal" class="nav-cta">Owner Sign-In</a>
  </div>
</header>

<main data-screen-label="Property Page">

  <!-- ————— Breadcrumb ————— -->
  <div class="wrap crumbs">
    <a class="back" href="{{ route('caumont') }}#portfolio"><span class="rule"></span><span>Portfolio</span></a>
    <span class="ref">N° 047</span>
  </div>

  <!-- ————— Title ————— -->
  <div class="wrap title-block">
    <div>
      <div class="eyebrow">
        <span>Cap d'Antibes · Côte d'Azur</span>
        <span class="dot"></span>
        <span class="m-sale">Off-Market Sale</span>
        <span class="m-rental">Annual Rental</span>
        <span class="m-stay">Short Stay</span>
      </div>
      <h1>Villa Estérel</h1>
      <p class="lede">A Belle-Époque seafront residence above the cape — restored without compromise in 2021, held within a private park of 1.4 hectares.</p>
    </div>

    <div class="keyfact m-sale">
      <div class="kf-label">Asking</div>
      <div class="kf-value"><em data-bind="sale-price">On request</em></div>
      <div class="kf-sub">Exclusive private mandate</div>
    </div>
    <div class="keyfact m-rental">
      <div class="kf-label">Rent</div>
      <div class="kf-value">€ 28,000 <em>/ month</em></div>
      <div class="kf-sub">Twelve months, renewable</div>
    </div>
    <div class="keyfact m-stay">
      <div class="kf-label">From</div>
      <div class="kf-value"><span data-bind="rate-key">€ 4,200</span> <em>/ night</em></div>
      <div class="kf-sub"><span class="star">✦</span> 4.97 · 142 stays</div>
    </div>
  </div>

  <!-- ————— Offered as ————— -->
  <div class="wrap">
    <div class="offer-tabs" role="tablist" aria-label="This residence is offered as">
      <button class="otab is-active" type="button" role="tab" aria-selected="true" data-mode-tab="sale">
        <span class="o-num">i.</span>
        <span class="o-label">For Sale</span>
        <span class="o-sub">Private mandate</span>
      </button>
      <button class="otab" type="button" role="tab" aria-selected="false" data-mode-tab="rental">
        <span class="o-num">ii.</span>
        <span class="o-label">Annual Rental</span>
        <span class="o-sub">Unfurnished or furnished</span>
      </button>
      <button class="otab" type="button" role="tab" aria-selected="false" data-mode-tab="stay">
        <span class="o-num">iii.</span>
        <span class="o-label">Short Stay</span>
        <span class="o-sub">Direct reservation</span>
      </button>
    </div>
  </div>

  <!-- ————— Gallery ————— -->
  <div class="wrap">
    <div class="gallery">
      <div class="cell cell-lead">
        <img src="https://images.unsplash.com/photo-1613490493576-7fde63acd811?w=1600&h=1000&fit=crop" alt="Villa Estérel · South Façade" id="esterel-facade">
        <span class="cap">Villa Estérel · South Façade</span>
      </div>
      <div class="cell">
        <img src="https://images.unsplash.com/photo-1600566753190-17f0baa2a6c3?w=900&h=700&fit=crop" alt="Seawater Pool" id="esterel-pool">
        <span class="cap">Seawater Pool · 25 m</span>
      </div>
      <div class="cell">
        <img src="https://images.unsplash.com/photo-1600585154340-be6161a56a0c?w=900&h=700&fit=crop" alt="Grand Salon" id="esterel-salon">
        <span class="cap">Grand Salon</span>
      </div>
      <div class="cell">
        <img src="https://images.unsplash.com/photo-1600607687939-ce8a79c367f3?w=900&h=700&fit=crop" alt="Bedroom I" id="esterel-bedroom">
        <span class="cap">Bedroom I · Sea View</span>
      </div>
      <div class="cell">
        <img src="https://images.unsplash.com/photo-1600596542815-ffad4c1539a9?w=900&h=700&fit=crop" alt="Park & grounds" id="esterel-park">
        <span class="cap">The Park · 1.4 ha</span>
      </div>
    </div>
    <div class="gallery-foot">
      <span class="note">Photography is released in full with the dossier.</span>
      <a class="all" href="#contact-card">+ 24 photographs</a>
    </div>
  </div>

  <!-- ————— Content ————— -->
  <div class="wrap content">

    <!-- ——— Left column ——— -->
    <article>

      <div class="block reveal">
        <div class="block-head">
          <h2>The <em>residence</em>.</h2>
          <span class="bnum">i.</span>
        </div>
        <div class="prose">
          <p>Villa Estérel stands at the water's edge on the eastern flank of the cape, its terraces falling south toward the sea. Built in 1908 and restored without compromise in 2021, the house holds seven bedrooms, a twenty-five-metre seawater pool, and a private pontoon beneath the pines.</p>
          <p>The park of 1.4 hectares is enclosed and unseen from the road. A guest cottage and staff apartment stand apart from the main house; cellar, garage and service quarters are reached separately, so the house runs without being noticed.</p>
        </div>
      </div>

      <div class="block reveal">
        <div class="block-head">
          <h2>Particulars.</h2>
          <span class="bnum">ii.</span>
        </div>
        <div class="specs-grid">
          <div class="spec"><span class="k">Surface</span><span class="v">820 m²</span></div>
          <div class="spec"><span class="k">Bedrooms</span><span class="v">7</span></div>
          <div class="spec"><span class="k">Bathrooms</span><span class="v">8</span></div>
          <div class="spec"><span class="k">Land</span><span class="v">1.4 ha</span></div>
          <div class="spec"><span class="k">Pool</span><span class="v">25 m <small>seawater</small></span></div>
          <div class="spec"><span class="k">Built</span><span class="v">1908 <small>rest. 2021</small></span></div>
          <div class="spec m-sale"><span class="k">Tenure</span><span class="v">Freehold</span></div>
          <div class="spec m-sale"><span class="k">Energy · DPE</span><span class="v">C</span></div>
          <div class="spec m-sale"><span class="k">Reference</span><span class="v">N° 047</span></div>
          <div class="spec m-rental"><span class="k">Furnished</span><span class="v">Fully <small>or unfurnished</small></span></div>
          <div class="spec m-rental"><span class="k">Available</span><span class="v">01 Sept 2026</span></div>
          <div class="spec m-rental"><span class="k">Pets</span><span class="v">By arrangement</span></div>
          <div class="spec m-stay"><span class="k">Guests</span><span class="v">Up to 14</span></div>
          <div class="spec m-stay"><span class="k">Staff</span><span class="v">Daily</span></div>
          <div class="spec m-stay"><span class="k">Minimum</span><span class="v">7 nights <small>in season</small></span></div>
        </div>
        <ul class="features" style="margin-top:36px;">
          <li>Direct sea access &amp; private pontoon</li>
          <li>South-facing terraces on three levels</li>
          <li>Guest cottage &amp; staff apartment</li>
          <li>Wine cellar, climate-controlled</li>
          <li>Garage for six cars</li>
          <li>Landscaped park, fully enclosed</li>
          <li>Smart-home &amp; security perimeter</li>
          <li>Summer kitchen &amp; dining loggia</li>
        </ul>
      </div>

      <!-- ——— SALE: terms & dossier ——— -->
      <div class="block reveal m-sale">
        <div class="block-head">
          <h2>Terms of <em>sale</em>.</h2>
          <span class="bnum">iii.</span>
        </div>
        <div class="ledger">
          <div class="row"><span class="k">Mandate</span><span class="v">Exclusive &amp; private — not advertised</span></div>
          <div class="row"><span class="k">Asking</span><span class="v"><em data-bind="sale-price">On request</em><small>Qualified enquiries receive the full financial dossier.</small></span></div>
          <div class="row"><span class="k">Fees</span><span class="v">Agency fees at seller's charge</span></div>
          <div class="row"><span class="k">Notary</span><span class="v">Étude Rochefort, Cannes</span></div>
          <div class="row"><span class="k">Completion</span><span class="v">Available to complete from October 2026</span></div>
        </div>
      </div>

      <div class="block reveal m-sale">
        <div class="block-head">
          <h2>The <em>dossier</em>.</h2>
          <span class="bnum">iv.</span>
        </div>
        <ul class="dossier">
          <li><span class="doc">Floor plans &amp; topographic survey</span><span class="tag">Under NDA</span></li>
          <li><span class="doc">Title, easements &amp; servitudes</span><span class="tag">Under NDA</span></li>
          <li><span class="doc">Technical diagnostics · DPE C</span><span class="tag gold">On request</span></li>
          <li><span class="doc">Renovation file, 2021 — architect's records</span><span class="tag">Under NDA</span></li>
          <li><span class="doc">Staff &amp; service contracts</span><span class="tag gold">On request</span></li>
        </ul>
        <p class="fine">The dossier is released after a signed confidentiality agreement and proof of funds.</p>
      </div>

      <!-- ——— RENTAL: lease ——— -->
      <div class="block reveal m-rental">
        <div class="block-head">
          <h2>The <em>lease</em>.</h2>
          <span class="bnum">iii.</span>
        </div>
        <div class="ledger">
          <div class="row"><span class="k">Rent</span><span class="v">€ 28,000 / month<small>Excluding charges.</small></span></div>
          <div class="row"><span class="k">Term</span><span class="v">Twelve months, renewable</span></div>
          <div class="row"><span class="k">Deposit</span><span class="v">Three months' rent</span></div>
          <div class="row"><span class="k">Charges</span><span class="v">€ 2,400 / month, estimated<small>Gardens, pool service and security; utilities at tenant's charge.</small></span></div>
          <div class="row"><span class="k">Available</span><span class="v">1 September 2026</span></div>
          <div class="row"><span class="k">References</span><span class="v">Banker's &amp; prior-landlord references required</span></div>
        </div>
      </div>

      <div class="block reveal m-rental">
        <div class="block-head">
          <h2>Included in the <em>tenancy</em>.</h2>
          <span class="bnum">iv.</span>
        </div>
        <ul class="features">
          <li>Weekly garden &amp; pool service</li>
          <li>House manager on call</li>
          <li>Security &amp; alarm monitoring</li>
          <li>Housekeeping optional, billed monthly</li>
        </ul>
      </div>

      <!-- ——— STAY: the stay + availability ——— -->
      <div class="block reveal m-stay">
        <div class="block-head">
          <h2>The <em>stay</em>.</h2>
          <span class="bnum">iii.</span>
        </div>
        <div class="ledger">
          <div class="row"><span class="k">Arrival</span><span class="v">From 16h00 · departure by 11h00</span></div>
          <div class="row"><span class="k">Guests</span><span class="v">Up to fourteen, across seven bedrooms</span></div>
          <div class="row"><span class="k">Staff</span><span class="v">House manager &amp; daily housekeeping included</span></div>
          <div class="row"><span class="k">Table &amp; cellar</span><span class="v">Private chef and sommelier on request</span></div>
          <div class="row"><span class="k">Season</span><span class="v">Seven-night minimum, June through September</span></div>
        </div>
      </div>

      <div class="block reveal m-stay">
        <div class="block-head">
          <h2>Availability.</h2>
          <span class="bnum">iv.</span>
        </div>
        <div class="cal-wrap">
          <div class="calendar">
            <div class="cal-head"><span>August 2026</span><span class="cal-nav">‹ · ›</span></div>
            <div class="cal-grid">
              <span class="dow">M</span><span class="dow">T</span><span class="dow">W</span><span class="dow">T</span><span class="dow">F</span><span class="dow">S</span><span class="dow">S</span>
              <span class="d off"></span><span class="d off"></span><span class="d off"></span><span class="d off"></span><span class="d off"></span><span class="d a">1</span><span class="d a">2</span>
              <span class="d a">3</span><span class="d a">4</span><span class="d b">5</span><span class="d b">6</span><span class="d b">7</span><span class="d b">8</span><span class="d b">9</span>
              <span class="d b">10</span><span class="d a">11</span><span class="d a">12</span><span class="d sel">13</span><span class="d sel">14</span><span class="d sel">15</span><span class="d sel">16</span>
              <span class="d sel">17</span><span class="d sel">18</span><span class="d sel">19</span><span class="d sel">20</span><span class="d a">21</span><span class="d a">22</span><span class="d a">23</span>
              <span class="d a">24</span><span class="d a">25</span><span class="d b">26</span><span class="d b">27</span><span class="d b">28</span><span class="d b">29</span><span class="d b">30</span>
              <span class="d b">31</span>
            </div>
            <div class="cal-legend">
              <span><i class="lg lg-a"></i>Available</span>
              <span><i class="lg lg-b"></i>Reserved</span>
              <span><i class="lg lg-sel"></i>Your stay</span>
            </div>
          </div>
          <div class="guest-word">
            <blockquote>“The house ran itself — we never saw the effort, only the result.”</blockquote>
            <div class="sig"><span>Guest · August 2025</span></div>
          </div>
        </div>
      </div>

      <!-- ——— Shared: situation ——— -->
      <div class="block reveal">
        <div class="block-head">
          <h2>Situation.</h2>
          <span class="bnum">v.</span>
        </div>
        <div class="map" data-caption="Carte · Cap d'Antibes · Shown approximately">
          <span class="pin"></span>
        </div>
        <div class="geo-row">
          <div class="geo"><span class="k">Nice Airport</span><span class="v">35 minutes</span></div>
          <div class="geo"><span class="k">Antibes Old Town</span><span class="v">10 minutes</span></div>
          <div class="geo"><span class="k">Sea</span><span class="v">Direct · private pontoon</span></div>
        </div>
        <p class="fine">
          <span class="m-sale m-rental">The precise address is shared once an enquiry has been qualified.</span>
          <span class="m-stay">Directions and access details follow a confirmed reservation.</span>
        </p>
      </div>

    </article>

    <!-- ——— Sidebar ——— -->
    <aside class="side" id="contact-card">

      <!-- SALE card -->
      <div class="side-card m-sale reveal delay-1">
        <span class="side-tag">Private Mandate</span>
        <div class="side-price">
          <span class="amt"><em data-bind="sale-price">On request</em></span>
        </div>
        <div class="side-lines">
          <div class="ln"><span>Mandate</span><span class="val">Exclusive · private</span></div>
          <div class="ln"><span>Tenure</span><span class="val">Freehold</span></div>
          <div class="ln"><span>Fees</span><span class="val">Seller's charge</span></div>
        </div>
        <button class="btn btn-solid" type="button">Request the dossier <span class="arrow">→</span></button>
        <button class="btn btn-line" type="button">Arrange a private viewing</button>
        <p class="side-note">Enquiries are answered personally within two working days.</p>
      </div>

      <!-- RENTAL card -->
      <div class="side-card m-rental reveal delay-1">
        <span class="side-tag">Annual Tenancy</span>
        <div class="side-price">
          <span class="amt">€ 28,000</span><span class="per">/ month</span>
        </div>
        <div class="side-lines">
          <div class="ln"><span>Term</span><span class="val">12 months min.</span></div>
          <div class="ln"><span>Deposit</span><span class="val">3 months</span></div>
          <div class="ln"><span>Charges</span><span class="val">€ 2,400 / mo est.</span></div>
          <div class="ln"><span>Available</span><span class="val gold">1 Sept 2026</span></div>
        </div>
        <button class="btn btn-solid" type="button">Request a tenancy file <span class="arrow">→</span></button>
        <button class="btn btn-line" type="button">Arrange a visit</button>
        <p class="side-note">References are reviewed before any visit is confirmed.</p>
      </div>

      <!-- STAY card -->
      <div class="side-card m-stay reveal delay-1">
        <span class="side-tag">Direct Reservation</span>
        <div class="side-price">
          <span class="amt" data-bind="rate">€ 4,200</span><span class="per">/ night</span>
        </div>
        <div class="bk-dates">
          <div class="bk-field"><span class="lbl">Arrival</span><span class="val">13 Aug 2026</span></div>
          <div class="bk-field"><span class="lbl">Departure</span><span class="val">20 Aug 2026</span></div>
        </div>
        <div class="bk-field bk-guests"><span class="lbl">Guests</span><span class="val">8 adults · 2 children</span></div>
        <div class="side-lines">
          <div class="ln"><span>7 nights</span><span class="val mono" data-bind="subtotal">€ 29,400</span></div>
          <div class="ln"><span>Concierge service</span><span class="val mono">€ 1,400</span></div>
          <div class="ln"><span>Cleaning</span><span class="val mono">€ 600</span></div>
        </div>
        <div class="bk-total"><span>Total</span><span class="t" data-bind="total">€ 31,400</span></div>
        <button class="btn btn-solid" type="button">Reserve privately <span class="arrow">→</span></button>
        <p class="side-note">No charge until the owner confirms · within 24 hours.</p>
      </div>

      <!-- Contact blocks -->
      <div class="side-agent m-sale m-rental">
        <span class="a-label">Your contact</span>
        <span class="a-name">Mathieu Vidal</span>
        <span class="a-line">Real Estate — Côte d'Azur</span>
        <a class="a-tel" href="tel:+33184601240">+33 1 84 60 12 40</a>
      </div>
      <div class="side-agent m-stay">
        <span class="a-label">Your house manager</span>
        <span class="a-name">Sébastien Roche</span>
        <span class="a-line">Attending before &amp; throughout your stay</span>
        <a class="a-tel" href="tel:+33184601240">+33 1 84 60 12 40</a>
      </div>

      <div class="payrow m-stay">
        <span>Settled by</span>
        <span class="pm">Visa</span>
        <span class="pm">Amex</span>
        <span class="pm">SEPA</span>
        <span class="pm">Wire</span>
      </div>

    </aside>
  </div>

</main>

<footer>
  <div class="wrap foot-inner">
    <a class="foot-brand" href="{{ route('caumont') }}">
      <img src="https://images.unsplash.com/photo-1613490493576-7fde63acd811?w=1600&h=1000&fit=crop" alt="">
      <span>Caumont</span>
    </a>
    <div class="foot-links">
      <a href="{{ route('caumont') }}#portfolio">Portfolio</a>
      <a href="{{ route('caumont') }}#contact">Contact</a>
      <span>© MMXXVI Caumont</span>
    </div>
  </div>
</footer>
