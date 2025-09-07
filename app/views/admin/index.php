<div class="row">
  <div class="col-md-3 mb-4">
    <div class="card accent-turq p-3">
      <div class="d-flex align-items-center">
        <div class="kpi-icon mr-3">📺</div>
        <div>
          <div class="card-title mb-1">Shows (Followed)</div>
          <div class="kpi"><?= (int)$stats['follows'] ?></div>
          <div class="kpi-sub">follows</div>
        </div>
      </div>
    </div>
  </div>

  <div class="col-md-3 mb-4">
    <div class="card accent-red p-3">
      <div class="d-flex align-items-center">
        <div class="kpi-icon mr-3">💬</div>
        <div>
          <div class="card-title mb-1">Comments</div>
          <div class="kpi"><?= (int)$stats['comments'] ?></div>
          <div class="kpi-sub">comments</div>
        </div>
      </div>
    </div>
  </div>

  <div class="col-md-3 mb-4">
    <div class="card accent-amber p-3">
      <div class="d-flex align-items-center">
        <div class="kpi-icon mr-3">👥</div>
        <div>
          <div class="card-title mb-1">Users</div>
          <div class="kpi"><?= (int)$stats['users'] ?></div>
          <div class="kpi-sub">users</div>
        </div>
      </div>
    </div>
  </div>

  <div class="col-md-3 mb-4">
    <div class="card accent-rose p-3">
      <div class="d-flex align-items-center">
        <div class="kpi-icon mr-3">🛡️</div>
        <div>
          <div class="card-title mb-1">Admins</div>
          <div class="kpi"><?= (int)$stats['admins'] ?></div>
          <div class="kpi-sub">admins</div>
        </div>
      </div>
    </div>
  </div>
</div>
