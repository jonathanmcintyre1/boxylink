<?php defined('ALTUMCODE') || die() ?>

<div class="d-flex flex-column flex-lg-row justify-content-between mb-4">
    <div>
        <div class="d-flex justify-content-between">
            <h1 class="h3"><i class="fa fa-fw fa-xs fa-code text-primary-900 mr-2"></i> <?= sprintf($this->language->admin_api_documentation->header) ?></h1>
        </div>
        <p class="text-muted"><?= $this->language->admin_api_documentation->subheader ?></p>
    </div>

    <div class="col-12 col-lg-4">
        <div class="form-group">
            <label for="api_key"><?= $this->language->admin_api_documentation->api_key ?></label>
            <input type="text" id="api_key" value="<?= $this->user->api_key ?>" class="form-control form-control-lg" readonly="readonly" />
        </div>

        <div class="form-group">
            <label for="base_url"><?= $this->language->admin_api_documentation->base_url ?></label>
            <input type="text" id="base_url" value="<?= SITE_URL . 'admin-api' ?>" class="form-control form-control-lg" readonly="readonly" />
        </div>
    </div>
</div>

<?php display_notifications() ?>

<div class="">

    <div class="mb-4">
        <h2 class="h4"><?= $this->language->admin_api_documentation->authentication->header ?></h2>
        <p class="text-muted"><?= $this->language->admin_api_documentation->authentication->subheader ?></p>
    </div>

    <div class="form-group">
        <label><?= $this->language->admin_api_documentation->example ?></label>
        <div class="card bg-gray-200 border-0">
            <div class="card-body">
                curl --request GET \<br />
                --url '<?= SITE_URL . 'admin-api/' ?><span class="text-primary">{endpoint}</span>' \<br />
                --header 'Authorization: Bearer <span class="text-primary">{api_key}</span>' \
            </div>
        </div>
    </div>

</div>

<hr class="border-gray-100 my-7" />

<div class="">

    <div class="mb-3">
        <h2 class="h4"><?= $this->language->admin_api_documentation->users->header ?></h2>
    </div>

    <div class="accordion">
        <div class="card">
            <div class="card-header bg-gray-200 p-3 position-relative">
                <h3 class="h6 m-0">
                    <a href="#" class="stretched-link" data-toggle="collapse" data-target="#users_read" aria-expanded="true" aria-controls="users_read">
                        <?= $this->language->admin_api_documentation->users->read->header ?>
                    </a>
                </h3>
            </div>

            <div id="users_read" class="collapse">
                <div class="card-body">
                    <div class="row">
                        <div class="col-12 col-lg-7 mb-4 mb-lg-0">

                            <div class="form-group mb-4">
                                <label><?= $this->language->admin_api_documentation->endpoint ?></label>
                                <div class="card bg-gray-200 border-0">
                                    <div class="card-body">
                                        <span class="badge badge-success mr-3">GET</span> <span class="text-muted"><?= SITE_URL ?>admin-api/users/</span><span class="text-primary">{user_id}</span>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group mb-4">
                                <label><?= $this->language->admin_api_documentation->example ?></label>
                                <div class="card bg-gray-200 border-0">
                                    <div class="card-body">
                                        curl --request GET \<br />
                                        --url '<?= SITE_URL ?>admin-api/users/<span class="text-primary">{user_id}</span>' \<br />
                                        --header 'Authorization: Bearer <span class="text-primary">{api_key}</span>' \
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <label><?= $this->language->admin_api_documentation->response ?></label>
                                <div class="card bg-gray-200 border-0">
                                        <pre class="card-body">
{
    "data": {
        "id":"1",
        "type":"users",
        "email":"example@example.com",
        "billing":{
            "type":"personal",
            "name":"John Doe",
            "address":"Lorem Ipsum",
            "city":"Dolor Sit",
            "county":"Amet",
            "zip":"5000",
            "country":"",
            "phone":"",
            "tax_id":""
        },
        "is_enabled":true,
        "plan_id":"custom",
        "plan_expiration_date":"2025-12-12 00:00:00",
        "plan_settings":{
            ...
        },
        "plan_trial_done":false,
        "language":"english",
        "timezone":"UTC",
        "country":null,
        "date":"2020-01-01 00:00:00",
        "last_activity":"2020-01-01 00:00:00",
        "total_logins":10
    }
}</pre>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-header bg-gray-200 p-3 position-relative">
                <h3 class="h6 m-0">
                    <a href="#" class="stretched-link" data-toggle="collapse" data-target="#users_create" aria-expanded="true" aria-controls="users_create">
                        <?= $this->language->admin_api_documentation->users->create->header ?>
                    </a>
                </h3>
            </div>

            <div id="users_create" class="collapse">
                <div class="card-body">

                    <div class="form-group mb-4">
                        <label><?= $this->language->admin_api_documentation->endpoint ?></label>
                        <div class="card bg-gray-200 border-0">
                            <div class="card-body">
                                <span class="badge badge-info mr-3">POST</span> <span class="text-muted"><?= SITE_URL ?>admin-api/users</span>
                            </div>
                        </div>
                    </div>

                    <div class="table-responsive table-custom-container mb-4">
                        <table class="table table-custom">
                            <thead>
                            <tr>
                                <th><?= $this->language->admin_api_documentation->parameters ?></th>
                                <th><?= $this->language->admin_api_documentation->details ?></th>
                                <th><?= $this->language->admin_api_documentation->description ?></th>
                            </tr>
                            </thead>
                            <tbody>
                            <tr>
                                <td>name</td>
                                <td><span class="badge badge-danger"><?= $this->language->admin_api_documentation->required ?></span></td>
                                <td>-</td>
                            </tr>
                            <tr>
                                <td>email</td>
                                <td><span class="badge badge-danger"><?= $this->language->admin_api_documentation->required ?></span></td>
                                <td>-</td>
                            </tr>
                            <tr>
                                <td>password</td>
                                <td><span class="badge badge-danger"><?= $this->language->admin_api_documentation->required ?></span></td>
                                <td>-</td>
                            </tr>
                            </tbody>
                        </table>
                    </div>

                    <div class="form-group mb-4">
                        <label><?= $this->language->admin_api_documentation->example ?></label>
                        <div class="card bg-gray-200 border-0">
                            <div class="card-body">
                                curl --request POST \<br />
                                --url '<?= SITE_URL ?>admin-api/users' \<br />
                                --header 'Authorization: Bearer <span class="text-primary">{api_key}</span>' \<br />
                                --header 'Content-Type: multipart/form-data' \<br />
                                --form 'name=<span class="text-primary">John Doe</span>' \<br />
                                --form 'email=<span class="text-primary">john@example.com</span>' \<br />
                                --form 'password=<span class="text-primary">MyStrongPassword123</span>'
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label><?= $this->language->admin_api_documentation->response ?></label>
                        <div class="card bg-gray-200 border-0">
                                        <pre class="card-body">
{
  "data": {
    "type": "users",
    "id": 2
  }
}</pre>
                        </div>
                    </div>

                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-header bg-gray-200 p-3 position-relative">
                <h3 class="h6 m-0">
                    <a href="#" class="stretched-link" data-toggle="collapse" data-target="#users_update" aria-expanded="true" aria-controls="users_update">
                        <?= $this->language->admin_api_documentation->users->update->header ?>
                    </a>
                </h3>
            </div>

            <div id="users_update" class="collapse">
                <div class="card-body">

                    <div class="form-group mb-4">
                        <label><?= $this->language->admin_api_documentation->endpoint ?></label>
                        <div class="card bg-gray-200 border-0">
                            <div class="card-body">
                                <span class="badge badge-info mr-3">POST</span> <span class="text-muted"><?= SITE_URL ?>admin-api/users/</span><span class="text-primary">{user_id}</span>
                            </div>
                        </div>
                    </div>

                    <div class="table-responsive table-custom-container mb-4">
                        <table class="table table-custom">
                            <thead>
                            <tr>
                                <th><?= $this->language->admin_api_documentation->parameters ?></th>
                                <th><?= $this->language->admin_api_documentation->details ?></th>
                                <th><?= $this->language->admin_api_documentation->description ?></th>
                            </tr>
                            </thead>
                            <tbody>
                            <tr>
                                <td>name</td>
                                <td><span class="badge badge-info"><?= $this->language->admin_api_documentation->optional ?></span></td>
                                <td>-</td>
                            </tr>
                            <tr>
                                <td>email</td>
                                <td><span class="badge badge-info"><?= $this->language->admin_api_documentation->optional ?></span></td>
                                <td>-</td>
                            </tr>
                            <tr>
                                <td>password</td>
                                <td><span class="badge badge-info"><?= $this->language->admin_api_documentation->optional ?></span></td>
                                <td>-</td>
                            </tr>
                            <tr>
                                <td>is_enabled</td>
                                <td><span class="badge badge-info"><?= $this->language->admin_api_documentation->optional ?></span></td>
                                <td><?= $this->language->admin_api_documentation->users->update->is_enabled ?></td>
                            </tr>
                            <tr>
                                <td>type</td>
                                <td><span class="badge badge-info"><?= $this->language->admin_api_documentation->optional ?></span></td>
                                <td><?= $this->language->admin_api_documentation->users->update->type ?></td>
                            </tr>
                            <tr>
                                <td>plan_id</td>
                                <td><span class="badge badge-info"><?= $this->language->admin_api_documentation->optional ?></span></td>
                                <td><?= $this->language->admin_api_documentation->users->update->plan_id ?></td>
                            </tr>
                            <tr>
                                <td>plan_expiration_date</td>
                                <td><span class="badge badge-info"><?= $this->language->admin_api_documentation->optional ?></span></td>
                                <td><?= $this->language->admin_api_documentation->users->update->plan_expiration_date ?></td>
                            </tr>
                            <tr>
                                <td>plan_trial_done</td>
                                <td><span class="badge badge-info"><?= $this->language->admin_api_documentation->optional ?></span></td>
                                <td><?= $this->language->admin_api_documentation->users->update->plan_trial_done ?></td>
                            </tr>
                            </tbody>
                        </table>
                    </div>

                    <div class="form-group mb-4">
                        <label><?= $this->language->admin_api_documentation->example ?></label>
                        <div class="card bg-gray-200 border-0">
                            <div class="card-body">
                                curl --request POST \<br />
                                --url '<?= SITE_URL ?>admin-api/users/<span class="text-primary">{user_id}</span>' \<br />
                                --header 'Authorization: Bearer <span class="text-primary">{api_key}</span>' \<br />
                                --header 'Content-Type: multipart/form-data' \<br />
                                --form 'name=<span class="text-primary">Jane Doe</span>' \<br />
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label><?= $this->language->admin_api_documentation->response ?></label>
                        <div class="card bg-gray-200 border-0">
                                        <pre class="card-body">
{
  "data": {
    "type": "users",
    "id": 2
  }
}</pre>
                        </div>
                    </div>

                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-header bg-gray-200 p-3 position-relative">
                <h3 class="h6 m-0">
                    <a href="#" class="stretched-link" data-toggle="collapse" data-target="#users_delete" aria-expanded="true" aria-controls="users_delete">
                        <?= $this->language->admin_api_documentation->users->delete->header ?>
                    </a>
                </h3>
            </div>

            <div id="users_delete" class="collapse">
                <div class="card-body">

                    <div class="form-group mb-4">
                        <label><?= $this->language->admin_api_documentation->endpoint ?></label>
                        <div class="card bg-gray-200 border-0">
                            <div class="card-body">
                                <span class="badge badge-danger mr-3">DELETE</span> <span class="text-muted"><?= SITE_URL ?>admin-api/users/</span><span class="text-primary">{user_id}</span>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label><?= $this->language->admin_api_documentation->example ?></label>
                        <div class="card bg-gray-200 border-0">
                            <div class="card-body">
                                curl --request DELETE \<br />
                                --url '<?= SITE_URL ?>admin-api/users/<span class="text-primary">{user_id}</span>' \<br />
                                --header 'Authorization: Bearer <span class="text-primary">{api_key}</span>' \<br />
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-header bg-gray-200 p-3 position-relative">
                <h3 class="h6 m-0">
                    <a href="#" class="stretched-link" data-toggle="collapse" data-target="#users_one_time_login_code" aria-expanded="true" aria-controls="users_one_time_login_code">
                        <?= $this->language->admin_api_documentation->users->one_time_login_code->header ?>
                    </a>
                </h3>
            </div>

            <div id="users_one_time_login_code" class="collapse">
                <div class="card-body">

                    <div class="form-group mb-4">
                        <label><?= $this->language->admin_api_documentation->endpoint ?></label>
                        <div class="card bg-gray-200 border-0">
                            <div class="card-body">
                                <span class="badge badge-info mr-3">POST</span> <span class="text-muted"><?= SITE_URL ?>admin-api/users/</span><span class="text-primary">{user_id}</span><span class="text-muted">/one-time-login-code</span>
                            </div>
                        </div>
                    </div>

                    <div class="form-group mb-4">
                        <label><?= $this->language->admin_api_documentation->example ?></label>
                        <div class="card bg-gray-200 border-0">
                            <div class="card-body">
                                curl --request POST \<br />
                                --url '<?= SITE_URL ?>admin-api/users/<span class="text-primary">{user_id}</span>/one-time-login-code' \<br />
                                --header 'Authorization: Bearer <span class="text-primary">{api_key}</span>' \<br />
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label><?= $this->language->admin_api_documentation->response ?></label>
                        <div class="card bg-gray-200 border-0">
                                        <pre class="card-body">
{
  "data": {
    "type": "users",
    "one_time_login_code": "7be875f9f1e3e73e1c7a09f186f6b69c",
    "url": "<?= SITE_URL ?>login/one-time-login-code/7be875f9f1e3e73e1c7a09f186f6b69c",
    "id": "1"
  }
}</pre>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>

</div>

