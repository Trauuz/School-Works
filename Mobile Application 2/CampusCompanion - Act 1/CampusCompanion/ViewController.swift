import UIKit

final class ViewController: UIViewController {
    @IBOutlet private weak var titleLabel: UILabel!
    @IBOutlet private weak var subtitleLabel: UILabel!
    @IBOutlet private weak var campusImageView: UIImageView!

    override func viewDidLoad() {
        super.viewDidLoad()
        titleLabel.text = "Campus Companion"
        subtitleLabel.text = "Your Campus, In Your Pocket"
        campusImageView.image = UIImage(systemName: "building.2.crop.circle")
    }

    @IBAction private func getStartedTapped(_ sender: UIButton) {
        subtitleLabel.text = "Let's get started!"
    }
}
