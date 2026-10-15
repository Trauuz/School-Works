import UIKit

class AnnouncementCell: UITableViewCell {
    @IBOutlet weak var categoryIconImageView: UIImageView!
    @IBOutlet weak var titleLabel: UILabel!
    @IBOutlet weak var dateLabel: UILabel!

    func configure(with announcement: CampusAnnouncement) {
        titleLabel.text = announcement.title
        dateLabel.text = "\(announcement.category ?? "") • \(announcement.date ?? "")\nLocation: \(announcement.location ?? "Not specified")\nPriority Level: \(announcement.priority ?? "Normal")"

        let isUrgent = announcement.priority == "Urgent"
        categoryIconImageView.image = UIImage(
            systemName: isUrgent ? "exclamationmark.triangle.fill" : "megaphone.fill"
        )
        categoryIconImageView.tintColor = isUrgent ? .systemRed : .systemGreen
        titleLabel.textColor = isUrgent ? .systemRed : .label
        dateLabel.textColor = .secondaryLabel
        accessoryType = .disclosureIndicator
    }
}
