<div class="switcher-body">
    <button class="btn btn-primary btn-switcher shadow-sm" type="button" data-bs-toggle="offcanvas"
        data-bs-target="#offcanvasScrolling" aria-controls="offcanvasScrolling"><i
            class="bx bx-cog bx-spin"></i></button>
    <div class="offcanvas offcanvas-end shadow border-start-0 p-2" data-bs-scroll="true" data-bs-backdrop="false"
        tabindex="-1" id="offcanvasScrolling">
        <div class="offcanvas-header border-bottom">
            <h5 class="offcanvas-title" id="offcanvasScrollingLabel">Theme Mode</h5>
            <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas"></button>
        </div>
        <div class="offcanvas-body">
            <!-- <h6 class="mb-0">Theme Variation</h6> -->
            <!-- <hr> -->
            <div class="form-check form-check-inline">
                <input class="form-check-input" type="radio" name="inlineRadioOptions" id="lightmode" value="option1" <?php echo isset($_SESSION['theam']) && $_SESSION['theam'] == "light" ? "checked" : "" ?>>
                <label class="form-check-label" for="lightmode"><i class="bi bi-brightness-alt-high-fill"></i> Light</label>
            </div>
            <hr>
            <div class="form-check form-check-inline">
                <input class="form-check-input" type="radio" name="inlineRadioOptions" id="darkmode" value="option2" <?php echo isset($_SESSION['theam']) && $_SESSION['theam'] == "dark" ? "checked" : "" ?>>
                <label class="form-check-label" for="darkmode"><i class="bi bi-moon-fill"></i> Dark</label>
            </div>
            <hr>
            <div class="form-check form-check-inline">
                <input class="form-check-input" type="radio" name="inlineRadioOptions" id="darksidebar" value="option3" <?php echo isset($_SESSION['theam']) && $_SESSION['theam'] == "semi-dark" ? "checked" : "" ?>>
                <label class="form-check-label" for="darksidebar"><i class="bi bi-cloud-sun-fill"></i> Semi Dark</label>
            </div>
            <!-- <hr>
            <div class="form-check form-check-inline">
                <input class="form-check-input" type="radio" name="inlineRadioOptions" id="ColorLessIcons"
                    value="option3">
                <label class="form-check-label" for="ColorLessIcons">Color Less Icons</label>
            </div> -->
        </div>
    </div>
</div>