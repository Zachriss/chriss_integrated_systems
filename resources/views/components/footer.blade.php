<footer class="footer-wrapper">
    <div class="footer-container">
        <div class="footer-line">
            <div class="footer-copyright">
                &copy; {{ date('Y') }} <span class="footer-school-name">{{ $system_settings->system_name ?? 'Chriss Integrated Systems' }}</span>. {{ $system_settings->footer_text ?? 'All rights reserved.' }}
            </div>
            <div class="footer-center d-none d-md-block">
                {{ $system_settings->email ? 'Contact: ' . $system_settings->email : 'Multi-Service ERP Platform' }}
                @if($system_settings->phone) &bull; {{ $system_settings->phone }} @endif
            </div>
            <div class="footer-credit">
                Developed by <span class="footer-school-name">{{ $system_settings->system_short_name ?? 'CIS' }} Team</span>
            </div>
        </div>
    </div>
</footer>
