export default function StepPhotos() {
    return (
        <div>
            <h2 className="text-xl font-extrabold text-ink mb-1.5">Fotók</h2>
            <p className="text-sm text-secondary mb-5">
                A képfeltöltés hamarosan elérhető lesz — most kihagyhatod ezt a lépést.
            </p>
            <div className="border-2 border-dashed border-[#cdc9c1] rounded-xl aspect-square flex flex-col items-center justify-center gap-2 text-[#8b877f]">
                <span className="text-3xl">+</span>
                <span className="text-sm">Hamarosan</span>
            </div>
        </div>
    )
}