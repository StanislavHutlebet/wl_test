<div style="height:100vh;display: flex;flex-direction: column;justify-content: center;align-items: center">
    <?php if (!is_user_logged_in()) : ?>
        <div id="wl_test">
            <ul class="nav nav-tabs" id="wl_test_tabs" role="tablist">
                <li class="nav-item">
                    <a class="nav-link active" href="#wl_test_login" id="wl_test_login-tab" data-toggle="tab" role="tab" aria-controls="wl_test_login" aria-selected="true"><?php _e('Login', 'wl_test')?></a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#wl_test_register" id="wl_test_register-tab" data-toggle="tab" role="tab" aria-controls="wl_test_register" aria-selected="false"><?php _e('Register', 'wl_test')?></a>
                </li>
            </ul>
            <div id="wl_tab_content" class="tab-content">
                <div class="tab-pane fade show active" id="wl_test_login" role="tabpanel" aria-labelledby="wl_test_login-tab">
                    <h2><?php _e('Login', 'wl_test')?></h2>  //Login
                    <form class="form-login">
                        <div class="form-group">
                            <label class="control-label" for="email"><?php _e('Email', 'wl_test')?></label>
                            <input type="email" id="email" name="email" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label class="control-label" for="password">Password</label>
                            <input type="password" id="password" name="password" class="form-control" required>
                        </div>
                        <div class="button_login">
                            <button class="btn btn-success" type="submit"><?php _e('Login', 'wl_test')?></button>
                        </div>
                    </form>
                </div>
                <div class="tab-pane fade" id="wl_test_register" role="tabpanel" aria-labelledby="wl_test_register-tab">
                    <h2><?php _e('Register', 'wl_test')?></h2>  //Register
                    <form class="form-register">
                        <div class="form-group">
                            <label class="control-label" for="reg_email"><?php _e('Email', 'wl_test')?></label>
                            <input type="email" class="form-control" name="email" id="reg_email" required>
                        </div>
                        <div class="form-group">
                            <label class="control-label" for="reg_password"><?php _e('Password', 'wl_test')?></label>
                            <input type="password" class="form-control" name="password" id="reg_password" required>
                        </div>
                        <div class="form-group">
                            <label class="control-label" for="reg_company"><?php _e('Company', 'wl_test')?></label>
                            <input type="text" class="form-control" name="company" id="reg_company" required>
                        </div>
                        <div class="form-group">
                            <label class="control-label" for="reg_position"><?php _e('Position', 'wl_test')?></label>
                            <input type="text" class="form-control" name="position" id="reg_position" required>
                        </div>
                        <div class="button_register">
                            <button class="btn btn-primary" type="submit"><?php _e('Register', 'wl_test')?></button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    <?php else : ?>
        <div class="alert alert-success">
            <?php _e('You are logged in', 'wl_test'); ?>
        </div>
    <?php endif; ?>
</div>