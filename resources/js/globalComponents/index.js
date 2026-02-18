export function registerGlobalComponents(app) {
const modules = import.meta.glob([
        '../**/*.vue'
    ], { eager: true })

    Object.entries(modules).forEach(([path, module]) => {

        const component = module.default
        if (!component) return

        // Remove ../ and .vue
        let name = path
            .replace('../', '')
            .replace('.vue', '')
            .split('/')

        // Remove generic folders
        name = name.filter(p => !['components','views','pages'].includes(p))

        // Convert to PascalCase
        const componentName = name
            .map(p => p.charAt(0).toUpperCase() + p.slice(1))
            .join('')

        app.component(componentName, component)

        console.log('Registered:', componentName)

    })
}
