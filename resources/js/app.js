import assessmentApp from './components/assessment'

document.addEventListener('alpine:init', () => {
    window.Alpine.data('assessmentApp', assessmentApp)
})
