<div class="language-switcher">
    <a href="{{ route('locale.switch', 'en') }}" 
       class="locale-link {{ app()->getLocale() === 'en' ? 'active' : '' }}"
       title="English">
        EN
    </a>
    <span class="locale-separator">|</span>
    <a href="{{ route('locale.switch', 'id') }}" 
       class="locale-link {{ app()->getLocale() === 'id' ? 'active' : '' }}"
       title="Bahasa Indonesia">
        ID
    </a>
</div>

<style scoped>
.language-switcher {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.5rem;
    background-color: #f3f4f6;
    border-radius: 0.5rem;
    border: 1px solid #e5e7eb;
}

.locale-link {
    padding: 0.375rem 0.75rem;
    text-decoration: none;
    font-weight: 600;
    font-size: 0.875rem;
    border-radius: 0.375rem;
    transition: all 0.2s ease;
    color: #6b7280;
}

.locale-link:hover {
    background-color: #e5e7eb;
    color: #374151;
}

.locale-link.active {
    background-color: #3b82f6;
    color: white;
    box-shadow: 0 2px 4px rgba(59, 130, 246, 0.3);
}

.locale-separator {
    color: #d1d5db;
    font-weight: 300;
}
</style>
